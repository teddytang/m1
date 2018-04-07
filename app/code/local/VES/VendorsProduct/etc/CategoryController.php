<?php
class VES_VendorsCategory_CategoryController extends Mage_Core_Controller_Front_Action {
	/**
	 * Initialize requested category object
	 *
	 * @return Mage_Catalog_Model_Category
	 */
	protected function _initCatagory()
	{
		Mage::dispatchEvent('vendor_catalog_controller_category_init_before', array('controller_action' => $this));
		$categoryId = (int) $this->getRequest()->getParam('id', false);
		if (!$categoryId) {
			return false;
		}
		$category = Mage::getModel('vendorscategory/category')
		->load($categoryId);
		if (!Mage::helper('vendorscategory')->canShow($category)) {
			return false;
		}

		if(!Mage::registry("vendor_id") && !Mage::registry("vendor") && !Mage::registry("current_vendor")){
			//echo $category->getVendorId();exit;
			$vendorObj = Mage::getModel('vendors/vendor')->load($category->getVendorId());

			//var_dump($vendorObj->getData());exit;
			Mage::register('vendor_id', $vendorObj->getVendorId());
			Mage::register('vendor', $vendorObj);
			Mage::register('current_vendor', $vendorObj);
		}
		
		Mage::register('current_vendor_category', $category);
		Mage::register('current_vendor_entity_key', $category->getPath());
		try {
			Mage::dispatchEvent(
			'vendor_catalog_controller_category_init_after',
			array(
			'category' => $category,
			'controller_action' => $this
			)
			);
		} catch (Mage_Core_Exception $e) {
			Mage::logException($e);
			return false;
		}
	
		return $category;
	}
	
	public function viewAction() {
		//echo "tesst";exit;
		if(!Mage::helper('vendorscategory')->moduleEnable()) {
			$this->_forward('noRoute');
			return;
		}
		if($category = $this->_initCatagory()) {
           if(Mage::app()->getRequest()->getQuery('ajaxVendorPage')){
					$this->loadLayout();
					$block = $this->getLayout()->getBlock('product_list');
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
				else{
					$this->loadLayout();
						$layout = $category->getCategoryLayout();
						if($layout) $this->getLayout()->getBlock('root')->setTemplate('page/'.$layout.'.phtml');
					$this->renderLayout();
				}
		}
		elseif (!$this->getResponse()->isRedirect()) {
			$this->_forward('noRoute');
		}
	}
	

}