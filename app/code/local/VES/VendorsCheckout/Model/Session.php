<?php

class VES_VendorsCheckout_Model_Session extends Mage_Core_Model_Session_Abstract
{
	/**
     * Class constructor. Initialize checkout session namespace
     */
    public function __construct()
    {
        $this->init('checkout');
    }
    protected $_quotes;
    
    protected $_quoteIds;
    
    
    protected function _getQuoteIdKey(){
    	return 'vendor_quote_id_' . Mage::app()->getStore()->getWebsiteId();
    }
    
    /**
     * Set quote ids
     * @param array $quoteIds
     */
    protected function _setQuoteIds($quoteIds = array()){
    	$this->setData($this->_getQuoteIdKey(),serialize($quoteIds));
    }
    
    /**
     * Get all vendor quote ids
     */
	public function getQuoteIds(){
    	$quoteIds = $this->getData($this->_getQuoteIdKey());
    	$quoteIds = unserialize($quoteIds);
    	
    	return $quoteIds;
    }
    
    /**
     * Get quote id by vendor id
     * @param int $vendorId
     * @see Mage_Checkout_Model_Session::getQuoteId()
     */
    public function getQuoteId($vendorId){
    	$quoteIds = $this->getQuoteIds();
    	return isset($quoteIds[$vendorId])?$quoteIds[$vendorId]:'';
    }

    
    
    /**
     * Set quote id by vendor id
     * @param int $vendorId
     * @param int $quoteId
     * @see Mage_Checkout_Model_Session::setQuoteId()
     */
    public function setQuoteId($vendorId, $quoteId){    	
    	/*Add the quote id to quote ids*/
    	$quoteIds = $this->getQuoteIds();
    	$quoteIds[$vendorId] = $quoteId;
    	$this->_setQuoteIds($quoteIds);
    }
    
    /**
     * Get all vendor quotes.
     */
    public function getQuotes(){
    	$quoteIds = $this->getQuoteIds();
/*    	if(!$quoteIds && !sizeof($quoteIds)){
    		$customerSession = Mage::getSingleton('customer/session');
    		if($customerSession->isLoggedIn()){
    			$customerQuoteByVendors = Mage::getModel('sales/quote')->getCollection()
					->addFieldToFilter('customer_id',Mage::getSingleton('customer/session')->getCustomerId())
					->addFieldToFilter('is_active',1)
					->addFieldToFilter('vendor_id',array('neq'=>0));
					
				if($customerQuoteByVendors->count()) foreach($customerQuoteByVendors as $quote){
					$this->setQuoteId($quote->getVendorId(), $quote->getId());
				}
    		}
    		$quoteIds = $this->getQuoteIds();
    	}*/
    	
    	if($quoteIds && is_array($quoteIds) && sizeof($quoteIds)) foreach($quoteIds as $vendorId=>$quoteId){
			if(!$vendorId) continue;
    		if(!isset($this->_quotes[$vendorId])  && ($quoteId !== null)){
    			$this->_quotes[$vendorId] = $this->getQuote($vendorId);
    		}
    	}
    	
    	return $this->_quotes;
    }
    
	/**
     * Get checkout quote instance by current session
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote($vendorId)
    {    	
        Mage::dispatchEvent('vendors_custom_quote_process', array('checkout_session' => $this));

        if (!isset($this->_quotes[$vendorId])) {
            /** @var $quote Mage_Sales_Model_Quote */
            $quote = Mage::getModel('vendorscheckout/quote')->setStoreId(Mage::app()->getStore()->getId());
        	if ($this->getQuoteId($vendorId)) {
                if ($this->_loadInactive) {
                    $quote->load($this->getQuoteId($vendorId));
                } else {
                    $quote->loadActive($this->getQuoteId($vendorId));
                }
                if ($quote->getId()) {
                    /**
                     * If current currency code of quote is not equal current currency code of store,
                     * need recalculate totals of quote. It is possible if customer use currency switcher or
                     * store switcher.
                     */
                    if ($quote->getQuoteCurrencyCode() != Mage::app()->getStore()->getCurrentCurrencyCode()) {
                        $quote->setStore(Mage::app()->getStore());
                        $quote->collectTotals()->save();
                        /*
                         * We mast to create new quote object, because collectTotals()
                         * can to create links with other objects.
                         */
                        $quote = Mage::getModel('sales/quote')->setStoreId(Mage::app()->getStore()->getId());
                        $quote->load($this->getQuoteId($vendorId));
                    }
                } else {
                    $this->setQuoteId($vendorId,null);
                }
            }
            
            $customerSession = Mage::getSingleton('customer/session');

            if (!$this->getQuoteId($vendorId)) {
                if ($customerSession->isLoggedIn() || $this->_customer) {
                    $customer = ($this->_customer) ? $this->_customer : $customerSession->getCustomer();
                    $quote->loadQuoteByCustomer($customer,$vendorId);
                    $this->setQuoteId($vendorId,$quote->getId());
                } else {
                    $quote->setIsCheckoutCart(true);
                    Mage::dispatchEvent('checkout_quote_init', array('quote'=>$quote));
                }
            }

			$quote->setVendorId($vendorId);
			
        	if ($this->getQuoteId($vendorId)) {
                if ($customerSession->isLoggedIn() || $this->_customer) {
                    $customer = ($this->_customer) ? $this->_customer : $customerSession->getCustomer();
                    $quote->setCustomer($customer);
                }
            }

            $quote->setStore(Mage::app()->getStore());
            
            /*Save the quote if it's not saved*/
            /*
            if(!$quote->getId()) {
            	$quote->getBillingAddress();
		        $quote->getShippingAddress()->setCollectShippingRates(true);
		        $quote->collectTotals();
            	$quote->save();
            	$this->setQuoteId($vendorId,$quote->getId());
            }
            */
            $this->_quotes[$vendorId] = $quote;
        }

        if ($remoteAddr = Mage::helper('core/http')->getRemoteAddr()) {
            $this->_quotes[$vendorId]->setRemoteIp($remoteAddr);
            $xForwardIp = Mage::app()->getRequest()->getServer('HTTP_X_FORWARDED_FOR');
            $this->_quotes[$vendorId]->setXForwardedFor($xForwardIp);
        }
        return $this->_quotes[$vendorId];
    }

   /**
     * Load data for customer quote and merge with current quote
     *
     * @return Mage_Checkout_Model_Session
     */
    public function loadCustomerQuote()
    {

        if (!Mage::getSingleton('customer/session')->getCustomerId()) {
            return $this;
        }

        Mage::dispatchEvent('load_customer_quote_before', array('checkout_session' => $this));
        $quotes = $this->getQuotes();
        if (count($quotes)) {
               foreach($this->getQuotes() as $quote){
                   if(!$quote->getVendorId()) continue;
                   $vendorId = $quote->getVendorId();

                   $customerQuote = Mage::getModel('vendorscheckout/quote')
                       ->setStoreId(Mage::app()->getStore()->getId())
                       ->loadQuoteByCustomer(
                           Mage::getSingleton('customer/session')->getCustomerId(),
                           $vendorId
                       );
                   if ($customerQuote->getId() && $this->getQuoteId($vendorId) != $customerQuote->getId()) {
                       if($this->getQuoteId($vendorId)){
                           $customerQuote->merge($quote)
                               ->collectTotals()
                               ->save();
                       }


                       $this->setQuoteId($vendorId,$customerQuote->getId());
                       $quote->delete();
                       $this->_quotes[$vendorId] = $customerQuote;
                   }

               }
       }else{
           $customerQuoteByVendors = Mage::getModel('vendorscheckout/quote')->getCollection()
               ->addFieldToFilter('customer_id',Mage::getSingleton('customer/session')->getCustomerId())
               ->addFieldToFilter('is_active',1)
               ->addFieldToFilter('vendor_id',array('neq'=>0));
           if($customerQuoteByVendors->count()) foreach($customerQuoteByVendors as $quote){
               $this->setQuoteId($quote->getVendorId(), $quote->getId());
           }
       }
       return $this;
    }

}