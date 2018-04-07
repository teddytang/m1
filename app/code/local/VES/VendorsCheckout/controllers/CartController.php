<?php
class VES_VendorsCheckout_CartController extends Mage_Core_Controller_Front_Action
{
	/**
     * Retrieve shopping cart model object
     *
     * @return Mage_Checkout_Model_Cart
     */
    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }
    
	public function clearAction(){
		$vendorId 	= $this->getRequest()->getParam('vendor');
		$vendor 	= Mage::getModel('vendors/vendor')->loadByVendorId($vendorId,'vendor_id');
		/* If vendor is not exist redirect to noRoute */
		if(!$vendorId || !$vendor->getId()){
			$this->norouteAction();
			return;
		}
		
		foreach($this->_getCart()->getQuote()->getAllItems() as $item){
			if($item->getVendorId() != $vendorId) continue;
			try {
				$this->_getCart()->removeItem($item->getId())
				  ->save();
			} catch (Exception $e) {
				$this->_getSession()->addError($this->__('Cannot remove the item.'));
				Mage::logException($e);
			}
		}
		
		$this->_redirect('checkout/cart/index');
	}
}