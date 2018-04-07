<?php

class VES_VendorsCheckout_Model_Checkout_Session extends Mage_Checkout_Model_Session
{
	public function loadCustomerQuoteByVendor(){
		$vendorsCheckoutSession = Mage::getSingleton('vendorscheckout/session');
		$sessionQuotes = $vendorsCheckoutSession->getQuotes();
		
		$customerQuotes = Mage::getModel('sales/quote')->getCollection()
						->addFieldToFilter('is_active',1)
						->addFieldToFilter('vendor_id',array('neq'=>0))
						->addFieldToFilter('customer_id',Mage::getSingleton('customer/session')->getCustomerId());
		if($customerQuotes->count()){
			if(sizeof($sessionQuotes)){
				foreach($sessionQuotes as $sessionQuote){
					$check = false;
					foreach($customerQuotes as $customerQuote){
						$vendorId 	= $customerQuote->getVendorId();
						if($vendorId == $sessionQuote->getVendorId()){
								$customerQuote->merge($sessionQuote)
				                    ->collectTotals()
				                    ->save();
								/*Delete session quote*/
				                $sessionQuote->delete();
				                $check = true;
						}
						if($vendorsCheckoutSession->getQuoteId($vendorId) != $customerQuote->getId())
							$vendorsCheckoutSession->setQuoteId($vendorId,$customerQuote->getId());
						
					}
					
					if(!$check){
						$sessionQuote->getBillingAddress();
			            $sessionQuote->getShippingAddress();
			            $sessionQuote->setCustomer(Mage::getSingleton('customer/session')->getCustomer())
			                ->setTotalsCollectedFlag(false)
			                ->collectTotals()
			                ->save();
					}
				}
			}else{
				foreach($customerQuotes as $customerQuote){
					$vendorsCheckoutSession->setQuoteId($customerQuote->getVendorId(),$customerQuote->getId());
				}
			}
		}else{
			foreach($sessionQuotes as $sessionQuote){
				$sessionQuote->getBillingAddress();
	            $sessionQuote->getShippingAddress();
	            $sessionQuote->setCustomer(Mage::getSingleton('customer/session')->getCustomer())
	                ->setTotalsCollectedFlag(false)
	                ->collectTotals()
	                ->save();
			}
		}
		
	}
	/**
     * Load data for customer quote and merge with current quote
     *
     * @return Mage_Checkout_Model_Session
     */
    public function loadCustomerQuote()
    {
        if(!Mage::helper('vendors')->moduleEnabled()) return parent::loadCustomerQuote();
        
    	/*Return if it's not advanced mode*/
    	if(Mage::helper('vendors')->getMode() != VES_Vendors_Model_Vendor::MODE_ADVANCED) return parent::loadCustomerQuote();
    	
        if (!Mage::getSingleton('customer/session')->getCustomerId()) {
            return $this;
        }

        Mage::dispatchEvent('load_customer_quote_before', array('checkout_session' => $this));

        $this->loadCustomerQuoteByVendor();
        
        $customerQuote = Mage::getModel('vendorscheckout/quote')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->loadByCustomer(Mage::getSingleton('customer/session')->getCustomerId());

        if ($customerQuote->getId() && $this->getQuoteId() != $customerQuote->getId()) {
            if ($this->getQuoteId()) {
                $customerQuote->merge($this->getQuote())
                    ->collectTotals()
                    ->save();
            }

            $this->setQuoteId($customerQuote->getId());

            if ($this->_quote) {
                $this->_quote->delete();
            }
            $this->_quote = $customerQuote;
        } else {
            $this->getQuote()->getBillingAddress();
            $this->getQuote()->getShippingAddress();
            $this->getQuote()->setCustomer(Mage::getSingleton('customer/session')->getCustomer())
                ->setTotalsCollectedFlag(false)
                ->collectTotals()
                ->save();
        }
        return $this;
    }
    
    
	/**
     * Get checkout quote instance by current session
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        if(!Mage::helper('vendors')->moduleEnabled()) return parent::getQuote();
        
    	/*Return if it's not advanced mode*/
    	if(Mage::helper('vendors')->getMode() != VES_Vendors_Model_Vendor::MODE_ADVANCED) return parent::getQuote();
    	
        Mage::dispatchEvent('custom_quote_process', array('checkout_session' => $this));

        if ($this->_quote === null) {
            /** @var $quote Mage_Sales_Model_Quote */
            $quote = Mage::getModel('sales/quote')->setStoreId(Mage::app()->getStore()->getId());
            if ($this->getQuoteId()) {
                if ($this->_loadInactive) {
                    $quote->load($this->getQuoteId());
                } else {
                    $quote->loadActive($this->getQuoteId());
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
                        $quote->load($this->getQuoteId());
                    }
                } else {
                    $this->setQuoteId(null);
                }
            }

            $customerSession = Mage::getSingleton('customer/session');

            if (!$this->getQuoteId()) {
                if ($customerSession->isLoggedIn() || $this->_customer) {
                    $customer = ($this->_customer) ? $this->_customer : $customerSession->getCustomer();
                    
                    /*$quote->loadByCustomer($customer);*/
                    Mage::getResourceModel('vendorscheckout/quote')->loadByCustomerId($quote,$customer->getId());
                    
                    $this->setQuoteId($quote->getId());
                } else {
                    $quote->setIsCheckoutCart(true);
                    Mage::dispatchEvent('checkout_quote_init', array('quote'=>$quote));
                }
            }

            if ($this->getQuoteId()) {
                if ($customerSession->isLoggedIn() || $this->_customer) {
                    $customer = ($this->_customer) ? $this->_customer : $customerSession->getCustomer();
                    $quote->setCustomer($customer);
                }
            }

            $quote->setStore(Mage::app()->getStore());
            $this->_quote = $quote;
        }

        if ($remoteAddr = Mage::helper('core/http')->getRemoteAddr()) {
            $this->_quote->setRemoteIp($remoteAddr);
            $xForwardIp = Mage::app()->getRequest()->getServer('HTTP_X_FORWARDED_FOR');
            $this->_quote->setXForwardedFor($xForwardIp);
        }
        return $this->_quote;
    }
}