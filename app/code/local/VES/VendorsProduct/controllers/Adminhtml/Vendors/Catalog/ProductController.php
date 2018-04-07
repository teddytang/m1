<?php
class VES_VendorsProduct_Adminhtml_Vendors_Catalog_ProductController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/vendors/catalog/pending_product');
    }
    
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('vendors/vendors')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function pendingAction() {
		$this->_initAction()
			->renderLayout();
	}
	
	/**
     * Product grid for AJAX request
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    
	public function massApproveAction()
    {
        $productIds = $this->getRequest()->getParam('product');
        if (!is_array($productIds)) {
            $this->_getSession()->addError($this->__('Please select product(s).'));
        } else {
            if (!empty($productIds)) {
                try {
                    foreach ($productIds as $productId) {
                        $product = Mage::getSingleton('catalog/product')->load($productId);
                        $product->setData('approval',VES_VendorsProduct_Model_Source_Approval::STATUS_APPROVED)->getResource()->saveAttribute($product,'approval');
                        Mage::helper('vendorsproduct')->sendProductReviewdNotificationEmail($product,VES_VendorsProduct_Model_Source_Approval::STATUS_APPROVED);
                    }
                    $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) have been approved.', count($productIds))
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('*/*/pending');
    }
    
	public function massRejectAction()
    {
        $productIds = $this->getRequest()->getParam('product');
        if (!is_array($productIds)) {
            $this->_getSession()->addError($this->__('Please select product(s).'));
        } else {
            if (!empty($productIds)) {
                try {
                    foreach ($productIds as $productId) {
                        $product = Mage::getSingleton('catalog/product')->load($productId);
                        $product->setData('approval',VES_VendorsProduct_Model_Source_Approval::STATUS_UNAPPROVED)->save();
                        Mage::helper('vendorsproduct')->sendProductReviewdNotificationEmail($product,VES_VendorsProduct_Model_Source_Approval::STATUS_UNAPPROVED);
                    }
                    $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) have been rejected.', count($productIds))
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('*/*/pending');
    }
	public function massDeleteAction()
    {
        $productIds = $this->getRequest()->getParam('product');
        if (!is_array($productIds)) {
            $this->_getSession()->addError($this->__('Please select product(s).'));
        } else {
            if (!empty($productIds)) {
                try {
                    foreach ($productIds as $productId) {
                        $product = Mage::getSingleton('catalog/product')->load($productId);
                        Mage::dispatchEvent('catalog_controller_product_delete', array('product' => $product));
                        $product->delete();
                    }
                    $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) have been deleted.', count($productIds))
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('*/*/index');
    }
}