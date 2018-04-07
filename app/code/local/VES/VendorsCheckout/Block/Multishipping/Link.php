<?php
class VES_VendorsCheckout_Block_Multishipping_Link extends Mage_Checkout_Block_Multishipping_Link
{
	public function getCheckoutUrl()
    {
        return $this->getUrl('vendorscheckout/multishipping', array('_secure'=>true));
    }
}
