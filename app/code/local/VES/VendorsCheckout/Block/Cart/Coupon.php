<?php

class VES_VendorsCheckout_Block_Cart_Coupon extends Mage_Checkout_Block_Cart_Coupon
{
	public function setQuote($quote){ 
		$this->_quote = $quote;
		return $this;
	}
	
	public function getCouponPostUrl(){
		$vendorId = $this->getQuote()->getVendorId();
		if(!$vendorId) return $this->getUrl('checkout/cart/couponPost');
		
		return $this->getUrl('checkout/cart/couponPost',array('vendor'=>$vendorId));
	}
}
