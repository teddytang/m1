<?php
class VES_VendorsCheckout_Block_Onepage_Link extends Mage_Checkout_Block_Onepage_Link
{
	public function getVendorId(){
		return $this->getQuote()->getVendorId();
	}
	public function getCheckoutUrl()
    {
    	if($this->getVendorId()) return $this->getUrl('checkout/onepage', array('_secure'=>true,'vendor'=>$this->getVendorId()));
    	return parent::getCheckoutUrl();
    }

    public function isDisabled()
    {
        return !$this->getQuote()->validateMinimumAmount();
    }

    public function isPossibleOnepageCheckout()
    {
        return $this->helper('checkout')->canOnepageCheckout();
    }
}