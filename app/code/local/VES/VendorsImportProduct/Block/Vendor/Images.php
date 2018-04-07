<?php
class VES_VendorsImportProduct_Block_Vendor_Images extends Mage_Adminhtml_Block_Template
{
	protected $_vendor;
	
	protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    /**
     * Get current logged in vendor
     */
    public function getVendor(){
    	if(!$this->_vendor){
    		$this->_vendor = Mage::getSingleton('vendors/session')->getVendor();
    	}
    	return $this->_vendor;
    }

    
    public function getTitle(){
    	return Mage::helper('vendorsimport')->__('Manage Product Images');
    }
    /**
     * Get image upload url
     */
    public function getUploadUrl(){
    	return Mage::getModel('adminhtml/url')->addSessionParam()->getUrl('vendors/import_images/upload');
    }
    
    public function getImageData(){
    	$images = array();
    	$helper = Mage::helper('vendorsimport');
    	$vendor = $this->getVendor();
    	$dir 	= $helper->getVendorImageFolder($vendor);
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
    	return json_encode($images);
    }
    
    public function getFileTypeExts(){
    	return '*.gif; *.jpg; *.png;*.bmp';
    }
    
    public function getFileTypeDesc(){
    	return $this->__('Media files only');
    }
    
    public function getRowTemplate(){
    	return '<tr><td class="a-center"><input class="image-checkbox" type="checkbox" name="images" value="{{file_name}}"></td><td class="a-center"><a href="{{url}}" target="_blank"><img src="{{thumbnail_url}}" width="40"/></a></td><td class="filename">{{file_name}}</td><td>{{file_size}}</td><td>{{last_modified}}</td></tr>';
    }
    public function getVendorConfig(){
    	$config = array(
    		'image_per_page'	=> 20,
    		'delete_url'		=> $this->getDeleteUrl(),
    		'row_template'		=> $this->getRowTemplate(),
    		'pager_template'	=> $this->__('Page {{current_page}} of {{page_count}} | Total {{total_items}} images found'),
    	);
    	return json_encode($config);
    }
    
    public function getDeleteUrl(){
    	return $this->getUrl('vendors/import_images/delete');
    }
}