<?php

class VES_VendorsCheckout_Block_Cart_Shipping extends Mage_Checkout_Block_Cart_Shipping
{
    protected $_vendor_id;
    /**
     * Get vendor id
     */
    public function getVendorId(){
        if(!isset($this->_vendor_id)){
            $this->_vendor_id = $this->getQuote()->getVendorId();
        }
        
        return $this->_vendor_id;
    }
	public function setQuote($quote){ 
		$this->_quote = $quote;
		return $this;
	}
	/**
	 * Get estimate post url
	 */
	public function getEstimatePostUrl(){
		$vendorId = $this->getVendorId();
		if(!$vendorId) return $this->getUrl('checkout/cart/estimatePost');
		
		return  $this->getUrl('checkout/cart/estimatePost',array('vendor'=>$vendorId));
	}
	
	/**
	 * Get estimate post url
	 */
	public function getEstimateUpdatePost(){
	    $vendorId = $this->getVendorId();
	    if(!$vendorId) return $this->getUrl('checkout/cart/estimateUpdatePost');
	    return  $this->getUrl('checkout/cart/estimateUpdatePost',array('vendor'=>$vendorId));
	}
}
