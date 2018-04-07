<?php

/**
 * One page checkout processing model
 */
class VES_VendorsCheckout_Model_Checkout_Type_Onepage extends Mage_Checkout_Model_Type_Onepage
{
	/**
     * Retrieve customer session vodel
     *
     * @return Mage_Customer_Model_Session
     */
    public function getCustomerSession()
    {
        return Mage::getSingleton('customer/session');;
    }

    /**
     * Retrieve customer object
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        return $this->getCustomerSession()->getCustomer();
    }
	/**
	 * 
	 * Get all shippable item by vendor
	 * @return array
	 */
	public function getShippableItemsByVendor(){
		$items = $this->getQuote()->getAllItems();
		$sortedItems = array();
		foreach($items as $item){
			if($item->getProduct()->getIsVirtual()) continue;
			$vendorId = $item->getProduct()->load($item->getProductId())->getVendorId();
			$transport = new Varien_Object(array('vendor_id'=>$vendorId,'item'=>$item));
			Mage::dispatchEvent('ves_vendors_checkout_init_vendor_id',array('transport'=>$transport));
			$vendorId = $transport->getVendorId();
			
			if(!isset($sortedItems[$vendorId])){
				$sortedItems[$vendorId] = array();
			}
			$sortedItems[$vendorId][] = $item;
		}
		return $sortedItems;
	}
	
	/**
	 * 
	 * Get all not shippable item by vendor
	 * @return array
	 */
	public function getNotShippableItemsByVendor(){
		$items = $this->getQuote()->getAllItems();
		$sortedItems = array();
		foreach($items as $item){
			if(!$item->getProduct()->getIsVirtual()) continue;
			$vendorId = $item->getProduct()->load($item->getProductId())->getVendorId();
			$transport = new Varien_Object(array('vendor_id'=>$vendorId,'item'=>$item));
			Mage::dispatchEvent('ves_vendors_checkout_init_vendor_id',array('transport'=>$transport));
			$vendorId = $transport->getVendorId();
			
			if(!isset($sortedItems[$vendorId])){
				$sortedItems[$vendorId] = array();
			}
			$sortedItems[$vendorId][] = $item;
		}
		return $sortedItems;
	}
	
	public function copyItemToQuote(Mage_Sales_Model_Quote_Address $address, Mage_Sales_Model_Quote $quote){
		/*Copy data from addresses quote to tmp quote*/
        foreach($address->getAllItems() as $item){
    		$quoteItemId = $item->getQuoteItemId();
    		$quoteItem = $this->getQuote()->getItemById($quoteItemId);
    			
    		$options = $quoteItem->getOptions();
    		$quoteItem->unsetData('item_id')->setQuote($quote)->save();
    			
    		foreach($options as $option){
    			$option->unsetData('option_id')->setData('item_id',$item->getId())->save();
    		}
    	}
	}
	/**
	 * Get Tmp Quote from addresses
	 * @param int $vendorId
	 * @param Mage_Sales_Model_Quote_Address $billingAddress
	 * @param Mage_Sales_Model_Quote_Address $shippingAddress
	 */
	public function getTmpQuote($vendorId,Mage_Sales_Model_Quote_Address $billingAddress, Mage_Sales_Model_Quote_Address $shippingAddress){
		$quote = Mage::getModel('sales/quote')->setStoreId(Mage::app()->getStore()->getId());
		$quote->setIsCheckoutCart(true)->setStore(Mage::app()->getStore())
				->setVendorId($vendorId);
		$tmpQuote = $quote;
		
    	if ($remoteAddr = Mage::helper('core/http')->getRemoteAddr()) {
            $tmpQuote->setRemoteIp($remoteAddr);
            $xForwardIp = Mage::app()->getRequest()->getServer('HTTP_X_FORWARDED_FOR');
            $tmpQuote->setXForwardedFor($xForwardIp);
        }
        
        $customerSession = Mage::getSingleton('customer/session');
    	if ($customerSession->isLoggedIn() || $this->_customer) {
        	$customer = ($this->_customer) ? $this->_customer : $customerSession->getCustomer();
        	$tmpQuote->setCustomer($customer);
        }
        /*Save Tmp Quote*/
        $tmpQuote->save();
        
        $this->copyItemToQuote($billingAddress, $tmpQuote);
        $this->copyItemToQuote($shippingAddress, $tmpQuote);
       	
        $billing = clone $billingAddress;
	    $billing->unsAddressId()->unsAddressType();
	    $billingData 		= $billing->exportCustomerAddress()->getData();
        
        
        $billing = Mage::getModel('sales/quote_address')
			       				->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_BILLING)
			       				->addData($billingData);
			       				if($customer = $this->getCustomer())$billing->setCustomerId($customer->getId());
		$tmpQuote->addAddress($billing);
		
		$shippingMethod		= $shippingAddress->getShippingMethod();
		$shipping 			= clone $shippingAddress;
		$shipping->unsAddressId()->unsAddressType();
	    $shippingData 		= $billing->exportCustomerAddress()->getData();
	    $shipping = Mage::getModel('sales/quote_address')
			       				->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_SHIPPING)
			       				->addData($shippingData)
			       				->setShippingMethod($shippingMethod);
			       				if($customer = $this->getCustomer())$billing->setCustomerId($customer->getId());
		$tmpQuote->addAddress($shipping);
		
		$tmpQuote->getShippingAddress()->setCollectShippingRates(true);
		$tmpQuote->setTotalsCollectedFlag(false)->collectTotals()->save();
		
		$payment = clone $this->getQuote()->getPayment();
		$payment->setPaymentId('')->save();
		$tmpQuote->addPayment($payment);
		
		$tmpQuote->getBillingAddress();
    	
        $tmpQuote->getShippingAddress()->setCollectShippingRates(true);
    	$tmpQuote->setTotalsCollectedFlag(false)->collectTotals()->save();
    	return $tmpQuote;
	}
	/**
	 * Prepare order base on quote addresses
	 * @param array $addresses
	 * @param int $vendorId
	 */
	public function _prepareOrderFromMultipleAddresses(array $addresses,$vendorId){
		if($addresses[0]->getAddressType() == Mage_Sales_Model_Quote_Address::TYPE_BILLING){
			$billingAddress = $addresses[0];
			$shippingAddress = $addresses[1];
		}else{
			$billingAddress = $addresses[1];
			$shippingAddress = $addresses[0];
		}
		
		$billingAddress->validate();
		$shippingAddress->validate();
		
		$quote = $this->getQuote();
        $quote->unsReservedOrderId();
        $quote->reserveOrderId();
		
		
		foreach($shippingAddress->getAllItems() as $item){
			
			 $quoteItem = $quote->getItemById($item->getQuoteItemId());
			 $item->setOriginalCustomPrice($quoteItem->getOriginalCustomPrice())
					->setCustomPrice($quoteItem->getCustomPrice())
					->setVendorId($quoteItem->getVendorId());
		}
		
        $shippingAddress->setCollectShippingRates(true)->collectTotals();
		
		
        //$quote->setTotalsCollectedFlag(false)->collectTotals();

        $tmpQuote = $this->getTmpQuote($vendorId,$billingAddress, $shippingAddress);
        
        //$tmpQuote->setTotalsCollectedFlag(false)->collectTotals();
        
        $convertQuote = Mage::getSingleton('sales/convert_quote');
        //$order = $convertQuote->addressToOrder($billingAddress);
        $order = $convertQuote->addressToOrder($tmpQuote->getShippingAddress());
        
        $order->setBillingAddress($convertQuote->addressToOrderAddress($tmpQuote->getBillingAddress()));		
		if ($tmpQuote->getBillingAddress()->getCustomerAddress()) {
            $order->getBillingAddress()->setCustomerAddress($tmpQuote->getBillingAddress()->getCustomerAddress());
        }
        
        $order->setShippingAddress($convertQuote->addressToOrderAddress($tmpQuote->getShippingAddress()));
		if ($tmpQuote->getShippingAddress()->getCustomerAddress()) {
        	$order->getShippingAddress()->setCustomerAddress($tmpQuote->getShippingAddress()->getCustomerAddress());
        }
        
        $order->setPayment($convertQuote->paymentToOrderPayment($tmpQuote->getPayment()));
        
        /*if (Mage::app()->getStore()->roundPrice($address->getGrandTotal()) == 0) {
            $order->getPayment()->setMethod('free');
        }*/
        
        $order->setQuote($tmpQuote);
        $order->setVendorId($vendorId);
        
        foreach ($tmpQuote->getAllItems() as $item) {            
            $orderItem = $convertQuote->itemToOrderItem($item);
            if ($item->getParentItem()) {
                $orderItem->setParentItem($order->getItemByQuoteItemId($item->getParentItem()->getId()));
            }
            $order->addItem($orderItem);
        }

        $tmpQuote->setIsActive(false)
	                ->save();
        return $order;
	}
	/**
     * Prepare order based on quote address
     *
     * @param   array $addresses
     * @return  Mage_Sales_Model_Order
     * @throws  Mage_Checkout_Exception
     */
    protected function _prepareOrder(array $addresses,$vendorId)
    {
    	if(sizeof($addresses)==2) return $this->_prepareOrderFromMultipleAddresses($addresses,$vendorId);
    	
    	
    	$address = $addresses[0];
    	$address->validate();
    	
        $quote = $this->getQuote();
        $quote->unsReservedOrderId();
        $quote->reserveOrderId();
		/*Fix custom price is not copied from quote item to address item*/
		foreach($address->getAllItems() as $item){
			 $quoteItem = $quote->getItemById($item->getQuoteItemId());
			 $item->setOriginalCustomPrice($quoteItem->getOriginalCustomPrice())
					->setCustomPrice($quoteItem->getCustomPrice())
					->setVendorId($quoteItem->getVendorId());
		}
		
        $address->setCollectShippingRates(true)->collectTotals();
		
        $convertQuote = Mage::getSingleton('sales/convert_quote');
        $order = $convertQuote->addressToOrder($address);
        $order->setQuote($quote);
        
        $billingAddress = ($address->getAddressType() == Mage_Sales_Model_Quote_Address::TYPE_SHIPPING)?$convertQuote->addressToOrderAddress($quote->getBillingAddress()):$convertQuote->addressToOrderAddress($address);
        $order->setBillingAddress($billingAddress);
        $order->setVendorId($vendorId);
		
        if ($address->getAddressType() == 'billing') {
            $order->setIsVirtual(1);
        } else {
            $order->setShippingAddress($convertQuote->addressToOrderAddress($address));
        }

        $order->setPayment($convertQuote->paymentToOrderPayment($quote->getPayment()));
        if (Mage::app()->getStore()->roundPrice($address->getGrandTotal()) == 0) {
            $order->getPayment()->setMethod('free');
        }

        foreach ($address->getItemsCollection() as $item) {
            $_quoteItem = $item->getQuoteItem();
            if (!$_quoteItem) {
                throw new Mage_Checkout_Exception(Mage::helper('checkout')->__('Item not found or already ordered'));
            }
            /*
			$item->setProductType($_quoteItem->getProductType())
                ->setProductOptions(
                    $_quoteItem->getProduct()->getTypeInstance(true)->getOrderOptions($_quoteItem->getProduct())
                );
			*/
            $orderItem = $convertQuote->itemToOrderItem($_quoteItem);
            if ($item->getParentItem()) {
                $orderItem->setParentItem($order->getItemByQuoteItemId($item->getParentItem()->getId()));
            }
            $order->addItem($orderItem);
        }

        return $order;
    }
    
    /**
     * Get shipping address by vendor id
     * @param unknown_type $vendorId
     */
    public function getShippingAddressByVendorId($vendorId){
    	foreach ($this->getQuote()->getAllShippingAddresses() as $address) {
            if (!$address->isDeleted() && $address->getAddressType()==Mage_Sales_Model_Quote_Address::TYPE_SHIPPING
                && $address->getVendorId() == $vendorId) {
                return $address;
            }
        }
        return false;
    }
    
    /**
     * Get billing address by vendor id
     * @param unknown_type $vendorId
     */
	public function getBillingAddressByVendorId($vendorId){
    	foreach ($this->getQuote()->getAllShippingAddresses() as $address) {
            if (!$address->isDeleted() && $address->getAddressType()==Mage_Sales_Model_Quote_Address::TYPE_BILLING
                && $address->getVendorId() == $vendorId) {
                return $address;
            }
        }
        return false;
    }
    
    /**
     * Create order based on checkout type. Create customer if necessary.
     *
     * @return Mage_Checkout_Model_Type_Onepage
     */
    public function saveOrder()
    {
        /*Do nothing if the extension is disabled*/
        if(!Mage::helper('vendors')->moduleEnabled()) return parent::saveOrder();
        
        
    	/*Return if it's not advanced mode*/
		if(Mage::helper('vendors')->getMode() != VES_Vendors_Model_Vendor::MODE_ADVANCED_X) return parent::saveOrder();
    	
		$isNewCustomer = false;
        switch ($this->getCheckoutMethod()) {
            case parent::METHOD_GUEST:
                $this->_prepareGuestQuote();
                break;
            case parent::METHOD_REGISTER:
                $this->_prepareNewCustomerQuote();
                $isNewCustomer = true;
                break;
            default:
                $this->_prepareCustomerQuote();
                break;
        }
    	$quote 					= $this->getQuote();
	    $shippableItems 		= $this->getShippableItemsByVendor();
	    $notShippableItems 		= $this->getNotShippableItemsByVendor();
       	if((sizeof($shippableItems) + sizeof($notShippableItems)) >=1){
	        $billingAddress		= $quote->getBillingAddress();
	        $shippingAddress 	= $quote->getShippingAddress();
	        $shippingMethod		= $shippingAddress->getShippingMethod();
	        $billing 		= clone $billingAddress;
	        $billing->unsAddressId()->unsAddressType();
	        $billingData 	= $billing->exportCustomerAddress()->getData();
	        
	        $shipping 		= clone $shippingAddress;
	        $shipping->unsAddressId()->unsAddressType();
			$shippingData 	= $shipping->exportCustomerAddress()->getData();
	        /**
	         * Remove all address of quote
	         */
	        foreach($quote->getAllShippingAddresses() as $address){
	        	$quote->removeAddress($address->getId());
	        }
 			
	        
	        $this->_checkoutSession->setLastQuoteId($this->getQuote()->getId())
            ->setLastSuccessQuoteId($this->getQuote()->getId())
            ->clearHelperData();
            
	        $addresses 			= array();
	        $count 				= 0;
       		$quote->setIsMultiShipping(true);
       		
       		/**
	         * Create shipping addresses for each vendor.
	         */
	       	foreach($shippableItems as $key=>$items){
	       		$shippingMethodObj = new Varien_Object(array('method'=>$shippingMethod));
	       		Mage::dispatchEvent('ves_vendor_checkout_type_onepage_shippingmethod',array('shipping_method'=>$shippingMethodObj, 'vendor_id'=>$key));
	       		$tmpShippingMethod = $shippingMethodObj->getMethod();
	       		
	       		
	       		$sAddress	= Mage::getModel('sales/quote_address')
		       				->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_SHIPPING)
		       				->addData($shippingData)
	                        ->setShippingMethod($tmpShippingMethod)
	                        ->setVendorId($key);
		       				if($customer = $this->getCustomer())$sAddress->setCustomerId($customer->getId());
		       				$this->getQuote()->addAddress($sAddress);
		       	
		       	foreach($items as $item){
		       		$sAddress->addItem($item)->setCollectShippingRates(true);
		       	}
		       	
		       	$addresses[$key][] = $sAddress;
	        }
	        
	        /**
	         * Create billing addresses for each vendor.
	         */
	        
	        if(sizeof($notShippableItems)){
		        $quote->removeAddress($quote->getBillingAddress()->getId());
		        
	       		foreach($notShippableItems as $key=>$items){
		       		$sAddress	= Mage::getModel('sales/quote_address')
			       				->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_BILLING)
			       				->addData($billingData)
		                        ->setVendorId($key);
			       				if($customer = $this->getCustomer())$sAddress->setCustomerId($customer->getId());
			       				$this->getQuote()->addAddress($sAddress);
			       	
			       	foreach($items as $item){
			       		$sAddress->addItem($item)->setCollectShippingRates(true);
			       	}
			       	$addresses[$key][] = $sAddress;
		        }
	        }
	        
			if($this->getCustomer() && $billingAddress->getSaveInAddressBook()){
    	        $customAddress = Mage::getModel('customer/address');
    	        $customAddress->setData($billingData)
    	        ->setCustomerId($this->getCustomer()->getId())
    	        ->setSaveInAddressBook('1');
    	        try {
    	            $customAddress->save();
    	        }
    	        catch (Exception $ex) {
    	            //Zend_Debug::dump($ex->getMessage());
    	        }
    	        
    	     }
	        
	        
	        if($this->getCustomer() && $shippingAddress->getSaveInAddressBook()){
    	         $customAddress = Mage::getModel('customer/address');
        	        $customAddress->setData($shippingData)
        	        ->setCustomerId($this->getCustomer()->getId())
        	        ->setSaveInAddressBook('1');
        	        try {
        	            $customAddress->save();
        	        }
        	        catch (Exception $ex) {
        	            //Zend_Debug::dump($ex->getMessage());
        	        }
	        }
			
	       	try {
	            foreach ($addresses as $vendorId=>$addresses) {
	            	//$address->validate();
	                $order 		= $this->_prepareOrder($addresses,$vendorId);
	                $orders[] 	= $order;
	            }

	            $lastOrderId 		= null;
	            $lastRealOrderId 	= null;
	       		
	       		/*Save new Customer*/
	            
	            if ($isNewCustomer) {
	            	$customer = $quote->getCustomer();
		            $customer->setGroupId(
						$customer->getOrigData('group_id')
			        );
		        	$customer->save();
		        	
		            try {
		                $this->_involveNewCustomer();
		            } catch (Exception $e) {
		                Mage::logException($e);
		            }
		        }
		        
	            foreach ($orders as $order) {
	                $order->place();
	                
	                $order->save();
	                if ($order->getCanSendNewEmailFlag()){
	                    $order->sendNewOrderEmail();
	                }
	                $lastOrderId = $order->getId();
	                $lastRealOrderId = $order->getIncrementId();
	                $orderIds[$order->getId()] = $order->getIncrementId();
	            }
	            
	            
				$redirectUrl = $this->getQuote()->getPayment()->getOrderPlaceRedirectUrl();
				
	            $this->_checkoutSession->setOrderIds($orderIds)
	            	->setLastQuoteId($this->getQuote()->getId())
	            	->setRedirectUrl($redirectUrl);

	            $this->getQuote()
	                ->setIsActive(false)
	                ->save();
	
	            Mage::dispatchEvent('checkout_submit_all_after', array('orders' => $orders, 'quote' => $this->getQuote()));
	
	            return $this;
	        } catch (Exception $e) {
	            Mage::dispatchEvent('checkout_multishipping_refund_all', array('orders' => $orders));
	            throw $e;
	        }
	        return $this;
       	}
       	return parent::saveOrder();
    }

}
