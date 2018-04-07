<?php

class VES_VendorsCredit_Model_Observer
{
    /**
     * Add credit for vendor without escrow.
     * @param unknown_type $observer
     */
    public function forceAddCreditToVendor($invoice){
        $order		= $invoice->getOrder();
        /* Add money to vendor account*/
        if($invoice->getState() != Mage_Sales_Model_Order_Invoice::STATE_PAID) return;
         
        /*Ignore commission calculation for individual payment method*/
        $paymentMethod = $order->getPayment()->getMethod();
        $flag = Mage::getStoreConfig('payment/'.$paymentMethod.'/ignore_commission_calculation');
        if($flag) return;
        
        /*Add credit to vendor (advanced mode)*/
        if(Mage::helper('vendors')->isAdvancedMode()){
            /*=========================ADVANCED, ADVANCED-X MODE===============================*/
            if(!$order->getVendorId()) return;
            
            $vendor		= Mage::getModel('vendors/vendor')->load($order->getVendorId());
            /*Do nothing if the vendor is not exist*/
            if(!$vendor->getId()) return;
            
            /*Return if the transaction is exist.*/
            $trans = Mage::getModel('vendorscredit/transaction')->getCollection()
                ->addFieldToFilter('type','order_payment')
                ->addFieldToFilter('additional_info',array('like'=>'%invoice|'.$invoice->getId().'%'));
            if($trans->count()) return;
            
            $amount = $invoice->getBaseGrandTotal();
            
            /*Create transaction to add invoice grandtotal to vendor credit account.*/
            $data = array(
                'vendor'	=> $vendor,
                'type'		=> 'order_payment',
                'amount'	=> $amount,
                'fee'		=> 0,
                'order'		=> $order,
                'invoice'	=> $invoice,
            );
            Mage::getModel('vendorscredit/type')->process($data);
            
            /*Calculate commission and create transaction for each item.*/
            foreach($invoice->getAllItems() as $item){
                $orderItem 	= $item->getOrderItem();
                if($orderItem->getParentItemId()) continue;
            
                $product    = Mage::getModel('catalog/product')->load($orderItem->getProductId());
            
                /*Continue if the transaction is exist.*/
                $trans 		= Mage::getModel('vendorscredit/transaction')->getCollection()
                ->addFieldToFilter('type','item_payment')
                ->addFieldToFilter('additional_info',array('like'=>'order_item|'.$orderItem->getId().'%'))
                ;
                if($trans->count()) continue;
        
                $amount 	= $item->getBaseRowTotal();
                $fee        = 0;
                $commissionObj  = new Varien_Object(array('fee'=>$fee));
                 
                Mage::dispatchEvent('ves_vendorscredit_calculate_commission',array(
                    'commission'=>$commissionObj,
                    'invoice_item'=>$item,
                    'product' => $product,
                    'vendor'=>$vendor,
                ));
        
                $fee    = $commissionObj->getFee();
                $additionalDescription = $commissionObj->getDescriptions();
                if($additionalDescription && is_array($additionalDescription)){
                    $tmpDescription = '<ul style="list-style: inside;">';
                    foreach($additionalDescription as $description){
                        $tmpDescription .='<li>'.$description.'</li>';
                    }
                    $tmpDescription .="</ul>";
                    
                    $additionalDescription = $tmpDescription;
                }
                
                $data = array(
                    'vendor'	=> $vendor,
                    'type'		=> 'item_commission',
                    'amount'	=> $fee,
                    'fee'		=> 0,
                    'item'		=> $item,
                    'order'		=> $order,
                    'invoice'	=> $invoice,
                    'additional_description' => $additionalDescription,
                );
                Mage::getModel('vendorscredit/type')->process($data);
            }
            
        }else{
            /*=========================GENERAL MODE===============================*/
            foreach($invoice->getAllItems() as $item){
                $orderItem 	= $item->getOrderItem();
                if($orderItem->getParentItemId()) continue;
            
                $product    = Mage::getModel('catalog/product')->load($orderItem->getProductId());
                $vendorId 	= $product->getVendorId();
                $vendor		= Mage::getModel('vendors/vendor')->load($vendorId);
            
                if($vendorId && $vendor->getId()){
                    /*Continue if the transaction is exist.*/
                    $trans 		= Mage::getModel('vendorscredit/transaction')->getCollection()
                    ->addFieldToFilter('type','item_payment')
                    ->addFieldToFilter('additional_info',array('like'=>'order_item|'.$orderItem->getId().'%'))
                    ;
                    if($trans->count()) continue;
            
                    $commissionObj  = new Varien_Object(array('fee'=>0));
                     
                    Mage::dispatchEvent('ves_vendorscredit_calculate_commission',array(
                        'commission'=>$commissionObj,
                        'invoice_item'=>$item,
                        'product' => $product,
                        'vendor'=>$vendor,
                    ));
            
                    $fee    = $commissionObj->getFee();
                    $additionalDescription = $commissionObj->getDescriptions();
                    if($additionalDescription && is_array($additionalDescription)){
                        $tmpDescription = '<ul style="list-style: inside;">';
                        foreach($additionalDescription as $description){
                            $tmpDescription .='<li>'.$description.'</li>';
                        }
                        $tmpDescription .="</ul>";
                    
                        $additionalDescription = $tmpDescription;
                    }
                    
                    //$amount = $item->getBaseRowTotal();
                    $amount = $item->getData('base_row_total') - $item->getData('base_discount_amount') + $item->getData('base_tax_amount'); /*Product price after discount (incl tax)*/
                    $data = array(
                        'vendor'	=> $vendor,
                        'type'		=> 'item_payment',
                        'amount'	=> $amount,
                        'fee'		=> $fee,
                        'item'		=> $item,
                        'order'		=> $order,
                        'invoice'	=> $invoice,
                        'additional_description' => $additionalDescription,
                    );
                    Mage::getModel('vendorscredit/type')->process($data);
                }
            }
        }
        
        
    }
    
	/**
	 * Add credit for vendor when an invoice is created. 
	 * @param unknown_type $observer
	 */
	public function sales_order_invoice_save_after($observer){
    	$invoice	= $observer->getInvoice();
    	$order		= $invoice->getOrder();
    	/* Add money to vendor account*/
    	if($invoice->getState() != Mage_Sales_Model_Order_Invoice::STATE_PAID) return;
    	
    	/*Ignore commission calculation for individual payment method*/
    	$paymentMethod = $order->getPayment()->getMethod();
    	$flag = Mage::getStoreConfig('payment/'.$paymentMethod.'/ignore_commission_calculation');
    	if($flag) return;
    	
    	/*If the escrow account is disabled then add money to vendor credit account.*/
    	if(!Mage::helper('vendorscredit')->isEnableEscrowAccount()) return $this->forceAddCreditToVendor($invoice);
    	

		
	   if(Mage::helper('vendors')->isAdvancedMode()){
    		/*ADVANCED MODE*/
	    	if(!$order->getVendorId()) return;
	    	
	    	$vendor		= Mage::getModel('vendors/vendor')->load($order->getVendorId());
	    	/*Do nothing if the vendor is not exist*/
	    	if(!$vendor->getId()) return;
	    	
	    	/*Return if the transaction is exist.*/
	    	$escrows		= Mage::getModel('vendorscredit/escrow')->getCollection()
	    					->addFieldToFilter('relation_id',$invoice->getId())
	    					;
	    	if($escrows->count()) return;
	    	
	    	$amount		= $invoice->getBaseGrandTotal();
	    	
	    	$data = array(
	    		'vendor_id'	     => $vendor->getId(),
	    		'relation_id'    => $invoice->getId(),
	    		'amount'         => $amount,
	    		'status'         => VES_VendorsCredit_Model_Escrow::STATUS_PENDING,
	    		'additional_info'    => 'order|'.$order->getId().'||invoice|'.$invoice->getId(),
	    	    'created_at'     => now(),
	    	);
	    	Mage::getModel('vendorscredit/escrow')->setData($data)->save();
    	}else{
    		/*GENERAL MODE*/
    		foreach($invoice->getAllItems() as $item){
    			$orderItem 	= $item->getOrderItem();
    			if($orderItem->getParentItemId()) continue;
    			
    			$vendorId 	= $orderItem->getVendorId();
    			$vendor		= Mage::getModel('vendors/vendor')->load($vendorId);
    			if($vendorId && $vendor->getId()){
    				/*Continue if the transaction is exist.*/
			    	$trans 		= Mage::getModel('vendorscredit/escrow')->getCollection()
			    					->addFieldToFilter('relation_id',$item->getId())
			    					;
			    	if($trans->count()) continue;
			    	
			    	$amount = $item->getData('base_row_total') - $item->getData('base_discount_amount') + $item->getData('base_tax_amount'); /*Product price after discount (incl tax)*/
			    	$data = array(
        	    		'vendor_id'	     => $vendor->getId(),
        	    		'relation_id'    => $item->getId(),
        	    		'amount'         => $amount,
        	    		'status'         => VES_VendorsCredit_Model_Escrow::STATUS_PENDING,
        	    		'additional_info'    => 'order|'.$order->getId().'||invoice|'.$invoice->getId().'||item|'.$item->getId(),
        	    	    'created_at'     => now(),
        	    	);
        	    	Mage::getModel('vendorscredit/escrow')->setData($data)->save();
    			}
    		}
    	}
    }
    
   /**
    * Add last 5 credit transactions grid to dashboard
    * @param Varien_Event_Observer $observer
    */
   public function vendor_dashboard_grids_preparelayout(Varien_Event_Observer $observer){
   		$grids = $observer->getGrids();
   		$grids->addTab('last_5_credit_transaction', array(
            'label'     => $grids->__('Last 5 Transactions'),
            'content'   => $grids->getLayout()->createBlock('vendorscredit/dashboard_transaction_grid')->toHtml(),
            'active'    => true
        ));
   }
   
   
   public function ves_vendors_account_edit_tab_main_before(Varien_Event_Observer $observer){
   		if(!Mage::registry('vendors_data') || !Mage::registry('vendors_data')->getId()) return;
   		
		$form = $observer->getForm();
		$fieldset = $form->addFieldset('credit_form', array('legend'=>Mage::helper('vendorscredit')->__('Credit Information'),'class'=>'fieldset-wide'));
		$fieldset->addType('credit_info','VES_VendorsCredit_Block_Form_Element_Credit_Info');
		$fieldset->addField('credit', 'credit_info', array(
		  'label'     => Mage::helper('vendorscredit')->__('Credit'),
		  'name'      => 'credit',
		));
   }


    public function releaseEscrow() {
        Mage::log('escrow release');

        $reminder = ' -'. Mage::getStoreConfig('vendors/credit/escrow_hold_time');
        $reminder .= ((int)$reminder > 1)? ' days' : 'day';
        $now = strtotime(now());
        $end = strtotime(date("Y-m-d H:i:s", $now) . $reminder); Mage::log(date("Y-m-d H:i:s",$end));

        $escrows = Mage::getModel('vendorscredit/escrow')->getCollection()
            ->addFieldToFilter('status',VES_VendorsCredit_Model_Escrow::STATUS_PENDING)
            ->addFieldToFilter('vendor_id', array('gt'=>'0'))
            ->addFieldToFilter('created_at',array('lt' => date("Y-m-d H:i:s",$end)));

        Mage::log($escrows->getSelect()->__toString());

        foreach($escrows as $_escrow) {
            Mage::log('escrow - '.$_escrow->getId());
           if($_escrow->canRelease()) {
               $_escrow->releasePayment();
           }
        }
    }
}