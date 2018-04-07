<?php
class VES_VendorsPage_IndexController extends Mage_Core_Controller_Front_Action
{
	public function indexAction(){

		$vendorId = Mage::registry('vendor_id');
		if($vendorId){
			
			$vendorObj = Mage::getModel('vendors/vendor')->loadByVendorId($vendorId);
			//if(!Mage::registry('vendor_id')) Mage::register('vendor_id', $vendorId);
			if($vendorObj->getId() && ($vendorObj->getStatus() == VES_Vendors_Model_Vendor::STATUS_ACTIVATED)){
				if(!Mage::app()->getRequest()->getQuery('ajaxVendorPage')){
					$this->loadLayout();
					$layout = Mage::helper('vendorsconfig')->getVendorConfig('design/home/layout',$vendorObj->getId());
					$this->getLayout()->getBlock('root')->setTemplate('page/'.$layout.'.phtml');
					$this->_title($vendorObj->getTitle());
					Mage::dispatchEvent('vendor_homepage_prepare_layout',array('vendor'=>$vendorObj,'front_action'=>$this));
					
					/*You can use Mage::registry('is_vendor_homepage') to check if the current page is homepage or not*/
					Mage::register('is_vendor_homepage', true);
					$this->renderLayout();
					return;
				}
				else{
					$this->loadLayout();
					$block = $this->getLayout()->getBlock('vendors.homepage.list');
					$content = $block->toHtml();
					$toolbarHtml = $block->getToolBarHtml();
					$content = str_replace($toolbarHtml, '', $content);
					$content = preg_replace('/\s+/', ' ', $content);
					$content = str_replace(array("\n", "\t",
						'<div class="category-products">',
						'<div class="toolbar-bottom"> </div> </div>'),
					'', $content);
					$data = array(
						'custom' => array(
							'product_list' => $content
						)
					);
					$data["status"] = true;
					echo json_encode($data);exit;
				}
				
			}
		}
		$this->_redirectUrl(Mage::getUrl());
	}
}