<?php
class VES_Vendors_CategoryController extends Mage_Core_Controller_Front_Action
{
    /**
     * Initialize requested category object
     *
     * @return Mage_Catalog_Model_Category
     */
    protected function _initCatagory()
    {
        Mage::dispatchEvent('catalog_controller_category_init_before', array('controller_action' => $this));
        $categoryId = (int) $this->getRequest()->getParam('id', false);
        if (!$categoryId) {
            return false;
        }

        $category = Mage::getModel('catalog/category')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($categoryId);

        if (!Mage::helper('catalog/category')->canShow($category)) {
            return false;
        }
        Mage::getSingleton('catalog/session')->setLastVisitedCategoryId($category->getId());
        Mage::register('current_category', $category);
        try {
            Mage::dispatchEvent('catalog_controller_category_init_after', array('category' => $category, 'controller_action' => $this));
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            return false;
        }
        return $category;
    }
    
    /**
     * Category view action
     */
    public function viewAction()
    {
        if ($category = $this->_initCatagory()) {
        	$vendorId = $category->getVendorId();
        	if($vendorId){
        		Mage::register('vendor_id', $vendorId);
				$vendorObj = Mage::getModel('vendors/vendors')->load($vendorId,'vendor_id');
				if($vendorObj->getId() && ($vendorObj->getStatus() == VES_Vendors_Model_Status::STATUS_APPROVED)){
		            $this->loadLayout();
					$config = unserialize($vendorObj->getConfig());
					if(isset($config['theme']) && $config['theme']){
						Mage::getDesign()->setTheme($config['theme']);
					}
					if(isset($config['layout']) && $config['layout']){
						$this->getLayout()->getBlock('root')->setTemplate('page/'.$config['layout'].'.phtml');
					}
		            if ($root = $this->getLayout()->getBlock('root')) {
		                $root->addBodyClass('categorypath-' . $category->getUrlPath())
		                    ->addBodyClass('category-' . $category->getUrlKey());
		            }
					
		            $this->_initLayoutMessages('catalog/session');
		            $this->_initLayoutMessages('checkout/session');
		            $this->renderLayout();
		            return;
				}
        	}
        }
        if (!$this->getResponse()->isRedirect()) {
            $this->_forward('noRoute');
        }
    }
}
