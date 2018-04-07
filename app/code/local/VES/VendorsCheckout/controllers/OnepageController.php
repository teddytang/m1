<?php
class VES_VendorsCheckout_OnepageController extends Mage_Core_Controller_Front_Action
{
	public function indexAction(){
		$vendorId 	= $this->getRequest()->getParam('vendor');
		$vendor 	= Mage::getModel('vendors/vendor')->load($vendorId);
		/* If vendor is not exist redirect to noRoute */
		if($vendorId && !$vendor->getId()){
			Mage::getSingleton('checkout/session')->addError('You are not allowed to access this page.');
			$this->_redirect('checkout/cart');
			return;
		}
		
		$checkoutSession = Mage::getSingleton('checkout/session');
		$tmpQuote = Mage::getSingleton('vendorscheckout/session')->getTmpQuote($vendorId);
		$this->_redirect('checkout/onepage/index',array('vendor'=>$vendorId));
	}
}