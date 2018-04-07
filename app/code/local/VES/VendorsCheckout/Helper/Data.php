<?php

class VES_VendorsCheckout_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
	 * Is one time checkout
	 * @return boolean
	 */
	public function isOnetimeCheckout(){
		return Mage::getStoreConfig('vendors/checkout/onetime_checkout');
	}
	
	/**
	 * Get onepage checkout URL
	 */
	public function getCheckoutUrl(){
		return Mage::getUrl('checkout',array('_secure' => true));
	}
	
	/**
	 * Get Cart URL
	 */
	public function getCartUrl(){
		return Mage::getUrl('checkout/cart/');
	}
	
	/**
	 * Get quote collection by main quote
	 * @param Mage_Sales_Model_Quote $mainQuote
	 * @return Mage_Sales_Model_Resource_Quote_Collection
	 */
	public function getQuoteCollectionByMainQuote(Mage_Sales_Model_Quote $mainQuote){
		return Mage::getModel('sales/quote')->getCollection()
		->addFieldToFilter('parent_quote',$mainQuote->getId())
		->addFieldToFilter('is_active',1);
	}
	
	/**
	 * Get quotes by main quote
	 * @param Mage_Sales_Model_Quote $mainQuote
	 * @return array
	 */
	public function getQuotesByMainQuote(Mage_Sales_Model_Quote $mainQuote){
		$quotes = array();
		foreach($this->getQuoteCollectionByMainQuote($mainQuote) as $quote){
			$quotes[$quote->getVendorId()] = $quote;
		}
		return $quotes;
	}
}