<?php

class VES_VendorsCredit_Model_Escrow extends Mage_Core_Model_Abstract
{
    const STATUS_PENDING    = 0;
    const STATUS_COMPLETED  = 1;
    const STATUS_CANCELED   = 2;
    
    protected $_vendor;
    
	public function _construct()
    {
        parent::_construct();
        $this->_init('vendorscredit/escrow');
    }
    
    /**
     * Process the additional information
     * @see Mage_Core_Model_Abstract::_afterLoad()
     */
    protected function _afterLoad(){
        $additionalInfo = explode("||",$this->getAdditionalInfo());
        $infoArr = array();
        foreach($additionalInfo as $info){
            $info = explode("|",$info);
            if(sizeof($info) == 2){
                $infoArr[$info[0]] = $info[1];
            }
        }
        $this->setData('information',$infoArr);
    }
    
    /**
     * Send escrow notification email to vendor.
     * @see Mage_Core_Model_Abstract::_afterSave()
     */
	protected function _afterSave()
    {
    	Mage::helper('vendorscredit')->sendEscrowNotificationEmail($this);
        return parent::_afterSave();
    }
    
    /**
     * Get vendor account relates to the escrow
     * @return VES_Vendors_Model_Vendor
     */
    public function getVendor(){
        if(!$this->_vendor){
            $this->_vendor = Mage::getModel('vendors/vendor')->load($this->getVendorId());
        }
        
        return $this->_vendor;
    }
    
    /**
     * Release the escrow payment.
     */
    public function releasePayment(){
        /*Only pending escrow payment can be released.*/
        if(!$this->canRelease()) {throw new Mage_Core_Exception(Mage::helper('vendorscredit')->__('Only pending escrow payment can be released'));}
        
        $vendor     = $this->getVendor();
        $amount     = $this->getAmount();
        $additionalInfo     = explode("||",$this->getAdditionalInfo());
        $info = array();
        
        if(Mage::helper('vendors')->isAdvancedMode()){
            foreach($additionalInfo as $value){
                $value = explode("|", $value);
                $info[$value[0]] = $value[1];
            }
            $order      = Mage::getModel('sales/order')->load(isset($info['order'])?$info['order']:'');
            $invoice    = Mage::getModel('sales/order_invoice')->load(isset($info['invoice'])?$info['invoice']:'');
            
            if(!$order->getId() || !$invoice->getId()) {throw new Mage_Core_Exception(Mage::helper('vendorscredit')->__('Order or invoice is not exist.'));}
            
            /*Add credit to vendor (advanced mode)*/
            
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
            /*General Mode*/
            foreach($additionalInfo as $value){
                $value = explode("|", $value);
                $info[$value[0]] = $value[1];
            }
            $order      = Mage::getModel('sales/order')->load(isset($info['order'])?$info['order']:'');
            $invoice    = Mage::getModel('sales/order_invoice')->load(isset($info['invoice'])?$info['invoice']:'');
            $item       = $invoice->getItemById(isset($info['item'])?$info['item']:'');
            $orderItem  = $item->getOrderItem();
            $product    = Mage::getModel('catalog/product')->load($orderItem->getProductId());
            
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
            
            $amount     = $this->getAmount();
            
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
        
        $this->setStatus(self::STATUS_COMPLETED)->save();
    }
    
    /**
     * Cancel the escrow payment
     */
    public function cancelPayment(){
        if(!$this->canCancel()) {throw new Mage_Core_Exception(Mage::helper('vendorscredit')->__('Only pending escrow payment can be canceled'));}
        
        $this->setStatus(self::STATUS_CANCELED)->save();
    }
    
    /**
     * Only pending transaction can be canceled.
     * @return boolean
     */
    public function canCancel(){
        return $this->getStatus()== self::STATUS_PENDING;
    }
    
    /**
     * Only pending transaction can be released.
     * @return boolean
     */
    public function canRelease(){
        return $this->getStatus()== self::STATUS_PENDING;
    }
    
    /**
     * Get Status options array
     * @return array
     */
    public function getStatusOptionsArray(){
        return array(
            self::STATUS_PENDING => Mage::helper('vendorscredit')->__('Pending'),
            self::STATUS_COMPLETED => Mage::helper('vendorscredit')->__('Completed'),
            self::STATUS_CANCELED => Mage::helper('vendorscredit')->__('Canceled'),
        );
    }
}