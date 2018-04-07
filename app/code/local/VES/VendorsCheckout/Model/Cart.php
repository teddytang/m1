<?php

class VES_VendorsCheckout_Model_Cart extends Mage_Checkout_Model_Cart
{
	public function getSession(){
		return Mage::getSingleton('vendorscheckout/session');
	}
	/**
     * Save cart
     *
     * @return Mage_Checkout_Model_Cart
     */
    public function save()
    {
        Mage::dispatchEvent('vendorscheckout_cart_save_before', array('cart'=>$this));

        $this->getQuote()->getBillingAddress();
        $this->getQuote()->getShippingAddress()->setCollectShippingRates(true);
        $this->getQuote()->collectTotals();
        $this->getQuote()->save();
        //$this->getCheckoutSession()->setQuoteId($this->getQuote()->getId());
        $this->getSession()->setQuoteId($this->getQuote()->getVendorId(),$this->getQuote()->getId());
        /**
         * Cart save usually called after changes with cart items.
         */
        Mage::dispatchEvent('vendorscheckoutcheckout_cart_save_after', array('cart'=>$this));
        return $this;
    }
}
