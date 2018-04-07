<?php

class VES_VendorsProductImage_Vendor_Catalog_ImageController extends VES_Vendors_Controller_Action
{
    public function uploadAction()
    {
        $productId = $this->getRequest()->getParam('product_id');
        $currentStoreId = Mage::app()->getStore()->getId();
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
        $product   = Mage::getModel('catalog/product')->load($productId);
        $storeId   = $this->getRequest()->getParam('store');
        if ($storeId)
        {
            $product->setStoreId($storeId);
        }
        $responce    = array();
        try {
            if(class_exists("Mage_Core_Model_File_Uploader")){
                $uploader = new Mage_Core_Model_File_Uploader('file_select[0]');
            }
            else{
                $uploader = new Varien_File_Uploader('file_select[0]');
            }
            $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
            $uploader->addValidateCallback('catalog_product_image',
            Mage::helper('catalog/image'), 'validateUploadFile');
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            $result = $uploader->save(
                Mage::getSingleton('catalog/product_media_config')->getBaseTmpMediaPath()
            );
            $result['url'] = Mage::getSingleton('catalog/product_media_config')->getTmpMediaUrl($result['file']);
            $result['file'] = $result['file'] . '.tmp';
           
			$responce = array(
        		'url'	=> $result['url'],
        		'file'	=> $result['file'],
        	);
            
        } catch (Exception $e) {
             $responce = array(
                'error' => $e->getMessage(),
                'errorcode' => $e->getCode());
        }
       
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($responce));
    }
    
    public function reloadtabAction()
    {
        $currentStoreId = Mage::app()->getStore()->getId();
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
        
        
        $productId = $this->getRequest()->getParam('product_id');
        $storeId   = $this->getRequest()->getParam('store');
        if (!$productId)
        {
            $response = $this->__('No product ID specified');
        }
        $product = Mage::getModel('catalog/product')->load($productId);
        if (!$product->getId())
        {
            $response = $this->__('Error occured while loading product');
        }
        if ($storeId)
        {
            $product->setStoreId($storeId);
        }

        // will save product image data first
        $imgUploadData = $this->getRequest()->getParam('vesimgupload');
        $productData   = $this->getRequest()->getParam('product');
        
      
        
        Mage::getModel('vendorsproductimage/observer')->saveMediaGallery($product, $imgUploadData, $productData);
        
        try
        {
            $product->save();
            Mage::app()->setCurrentStore($currentStoreId);
            $block = Mage::app()->getLayout()->createBlock('vendorsproductimage/catalog_product_helper_form_gallery_content', 'vesupload_tab_images', array('product' => $product));
            $this->getResponse()->setBody($block->toHtml());
        } catch (Mage_Core_Exception $e) {
            $this->getResponse()->setBody($e->getMessage());
        }
        catch (Exception $e) {
            $this->getResponse()->setBody(Mage::helper('catalog')->__('Error saving product information ' . $e));
            Mage::logException($e);
        }
    }

    protected function _isAllowed()
    {
		return true;
        return Mage::getSingleton('admin/session')->isAllowed('catalog/products');
    }
}