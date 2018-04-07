<?php
class VES_VendorsCheckout_Block_Default extends Mage_Core_Block_Template
{
	/**
	 * Replace the top cart link by the new one.
	 */
	protected function _prepareLayout(){
	    /*Do nothing if the extension is disabled*/
	    if(!Mage::helper('vendors')->moduleEnabled()) return;
	    
		if(Mage::helper('vendors')->getMode() != VES_Vendors_Model_Vendor::MODE_ADVANCED) return;
		
		$topLinkBlock = $this->getLayout()->getBlock('top.links');
		if($topLinkBlock){
			$topLinkBlock->removeLinkByUrl($this->helper('vendorscheckout')->getCheckoutUrl());
			/* Remove default Cart URL */
			$topLinkBlock->removeLinkByUrl($this->helper('vendorscheckout')->getCartUrl());

			$vendorCheckoutSession = Mage::getSingleton('vendorscheckout/session');
			
			$quotes = $vendorCheckoutSession->getQuotes();
			//$quotes[] = Mage::getSingleton('checkout/session')->getQuote();
			$count = 0;
			if($quotes && sizeof($quotes)) foreach($quotes as $quote){
				$count += $quote->getItemsQty();
			}
			
			
            if ($count == 1) {
                $text = $this->__('My Cart (%s item)', $count);
            } elseif ($count > 0) {
                $text = $this->__('My Cart (%s items)', $count);
            } else {
                $text = $this->__('My Cart');
            }

            $topLinkBlock->addLink($text, 'checkout/cart', $text, true, array(), 50, null, 'class="top-link-cart"');
        }
        
		return parent::_prepareLayout();
	}
}