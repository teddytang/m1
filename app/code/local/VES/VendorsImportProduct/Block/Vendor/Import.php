<?php
class VES_VendorsImportProduct_Block_Vendor_Import extends Mage_Adminhtml_Block_Template
{
	protected $_vendor;
	

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
    	return Mage::helper('vendorsimport')->__('Manage CSV Import Files');
    }
    /**
     * Get image upload url
     */
    public function getUploadUrl(){
    	return Mage::getModel('adminhtml/url')->addSessionParam()->getUrl('vendors/import_index/upload');
    }
    
    public function getImageData(){
    	$images = array();
    	$helper = Mage::helper('vendorsimport');
    	$vendor = $this->getVendor();
    	$dir 	= $helper->getVendorImportFolder($vendor);
	    if ($handle = opendir($dir)) {
		
	        while (false !== ($entry = readdir($handle))) {
		        if ($entry != "." && $entry != ".." && is_file($dir.$entry)) {
		            $images[] = array(
		            	'url'			=> $helper->getVendorImportFileUrl($entry,$vendor),
		            	'thumbnail_url'	=> $this->getSkinUrl('ves_vendors/importproduct/icons/file.png'),
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
    	return '*.csv; *.xml';
    }
    
    public function getFileTypeDesc(){
    	return $this->__('CSV,XML files only');
    }
    
    public function getRowTemplate(){
    	return '<tr><td class="a-center"><input class="image-checkbox" type="checkbox" name="images" value="{{file_name}}"></td><td class="a-center"><img src="{{thumbnail_url}}"/></td><td class="filename"><a href="{{url}}">{{file_name}}</a><div class="ves-vendorimport"></div></td><td>{{file_size}}</td><td>{{last_modified}}</td><td class="a-center"><a href="javascript: void(0);" onclick="vesVendorImport(\'{{file_name}}\',$(this))">'.$this->__('Import').'</a></td></tr>';
    }
    
    public function getVendorConfig(){
    	$config = array(
    		'image_per_page'	=> 20,
    		'delete_url'		=> $this->getDeleteUrl(),
    		'row_template'		=> $this->getRowTemplate(),
    		'pager_template'	=> $this->__('Page {{current_page}} of {{page_count}} | Total {{total_items}} files found'),
    	);
    	return json_encode($config);
    }
    
    public function getDeleteUrl(){
    	return $this->getUrl('vendors/import_index/delete');
    }
    
    public function getSampleCsvHtml(){
    	if($blockId = Mage::getStoreConfig('vendors/vendors_import_export/sample_import_files')){
    		$block = Mage::getBlockSingleton('cms/block')->setBlockId($blockId);
    		return $block->toHtml();
    	}
    	return '';
    }
}