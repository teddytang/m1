<?php
class VES_VendorsImportProduct_Import_ImagesController extends VES_Vendors_Controller_Action
{   
	protected function _isAllowed()
    {
        return Mage::helper('vendorsimport')->moduleEnable();
    }
    
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('vendorsimport')
			->_title($this->__('Import/Export'))
			->_title($this->__('Manage Images'))
			->_addBreadcrumb($this->__('Import/Export'), $this->__('Import/Export'))
			->_addBreadcrumb($this->__('Manage Images'), $this->__('Manage Images'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}
	
	public function uploadAction()
    {
        try {
            $vendor = $this->_getSession()->getVendor();
            $helper	= Mage::helper('vendorsimport');
        	$uploader = new Mage_Core_Model_File_Uploader('Filedata');
            $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
            $uploader->addValidateCallback('catalog_product_image',
                Mage::helper('catalog/image'), 'validateUploadFile');
            $uploader->setAllowRenameFiles(false);
            $uploader->setFilesDispersion(false);
            $result = $uploader->save(
                $helper->getVendorImageFolder($vendor)
            );

            /**
             * Workaround for prototype 1.7 methods "isJSON", "evalJSON" on Windows OS
             */
            $result['tmp_name'] = str_replace(DS, "/", $result['tmp_name']);
            $result['path'] 	= str_replace(DS, "/", $result['path']);
            
            $result['file_name']= $result['file'];
            $result['file_size']= $result['size'];
            $result['last_modified']	= Mage::app()->getLocale()->date(filemtime($result['path'] .$result['file'] ))->toString(Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM));
            $result['url'] 				= $helper->getVendorImageUrl($result['file'],$vendor);
            $result['thumbnail_url']	= $helper->getVendorImageUrl($result['file'],$vendor,$helper->getThumbnailSize());
            $result['success']	= true;

        } catch (Exception $e) {
            $result = array(
            	'success'	=> false,
                'error' => $e->getMessage(),
                'errorcode' => $e->getCode());
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
    
    public function deleteAction(){
    	$vendor = $this->_getSession()->getVendor();
    	$helper = Mage::helper('vendorsimport');
    	$files 	= explode(',',$this->getRequest()->getParam('files'));
    	foreach($files as $file){
    		//@unlink(Mage::helper('vendorsimport')->getVendorImageFolder($vendor).$file);
    		Mage::helper('vendorsimport/image')->init('ves_vendorsimportproduct/'.$vendor->getVendorId().'/media/'.$file,$vendor)->resize($helper->getThumbnailSize())->delete();
    	}
    	
    	$dir 	= $helper->getVendorImageFolder($vendor);
    	$images = array();
	    if ($handle = opendir($dir)) {
	        while (false !== ($entry = readdir($handle))) {
		        if ($entry != "." && $entry != ".." && is_file($dir.$entry)) {
		            $images[] = array(
		            	'url'			=> $helper->getVendorImageUrl($entry,$vendor),
		            	'thumbnail_url'	=> $helper->getVendorImageUrl($entry,$vendor,$helper->getThumbnailSize()),
		            	'file_name'		=> $entry,
		            	'file_size' 	=> Mage::getModel('directory/currency')->format(filesize($dir.$entry),array('display'=>Zend_Currency::NO_SYMBOL,precision=>0),false),
		            	'last_modified'	=> Mage::app()->getLocale()->date(filemtime($dir.$entry))->toString(Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM))
		            );
		        }
		    }
		}
    	$result = array('success'=>true,'images'=>$images);
    	$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
}