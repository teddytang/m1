<?php

class VES_VendorsGroup_Model_Observer_Product
{	
	/**
	 * Check if the message module is enable
	 * @param Varien_Event_Observer $observer
	 */
	public function catalog_product_save_before(Varien_Event_Observer $observer){
		$product 	= $observer->getEvent()->getProduct();
		/*Check only when vendor add new product*/
		if(!$product->isObjectNew() || $product->getId()) return;
		
		$vendorId 	= $product->getVendorId();
		if($vendorId){
			$vendor 		= Mage::getModel('vendors/vendor')->load($vendorId);
			$groupId 		= $vendor->getGroupId();
			$productLimit 	= Mage::helper('vendorsgroup')->getConfig('product/product_limit',$groupId);
			if($productLimit){
				$productCollection = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('vendor_id',$vendorId);
				if($productLimit<=$productCollection->count()){
					throw new VES_VendorsGroup_Exception(Mage::helper('vendorsgroup')->__('You can only add maximum %s products',$productLimit),VES_VendorsGroup_Exception::ERR_PRODUCT_LIMITATION);
				}
			}
		}
	}
	
	public function controller_action_predispatch_vendors_catalog_product_new(Varien_Event_Observer $observer){
		$session = Mage::getSingleton('vendors/session');
		$vendor = $session->getVendor();
		if($vendor && $vendor->getId()){
			$groupId 		= $vendor->getGroupId();
			$productLimit 	= Mage::helper('vendorsgroup')->getConfig('product/product_limit',$groupId);
			if($productLimit){
				$productCollection = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('vendor_id',$vendor->getId());
				if($productLimit<=$productCollection->count()){
					$session->addError(Mage::helper('vendorsgroup')->__('You can only add maximum %s products',$productLimit),VES_VendorsGroup_Exception::ERR_PRODUCT_LIMITATION);
					$controllerAction = $observer->getControllerAction();
					$controllerAction->setFlag('', 'no-dispatch', true);
					$controllerAction->setRedirectWithCookieCheck('vendors/catalog_product');
				}
			}
		}
	}
}