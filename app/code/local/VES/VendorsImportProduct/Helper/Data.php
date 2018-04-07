<?php

class VES_VendorsImportProduct_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
	 * Is Module Enable
	 */
	public function moduleEnable(){
		$result = new Varien_Object(array('module_enable'=>true));
		Mage::dispatchEvent('ves_vendorsimportproduct_module_enable',array('result'=>$result));
		return $result->getData('module_enable') && sizeof($this->getExportProfile());
	}
	
	public function getImportProfile($extensionName){
		switch($extensionName){
			case "csv":
				return Mage::getStoreConfig('vendors/vendors_import_export/csv_import_profile');
			case "xml":
				return Mage::getStoreConfig('vendors/vendors_import_export/xml_import_profile');
		}
		
		return Mage::getStoreConfig('vendors/vendors_import_export/csv_import_profile');
	}
	
	public function getExportProfile(){
		return explode(",",Mage::getStoreConfig('vendors/vendors_import_export/export_profile'));
	}
	/**
	 * Get image folder of vendor
	 * @param VES_Vendors_Model_Vendor $vendor
	 * @return string
	 */
	public function getVendorImageFolder(VES_Vendors_Model_Vendor $vendor){
		return Mage::getBaseDir('media').DS.'ves_vendorsimportproduct'.DS.$vendor->getVendorId().DS.'media'.DS;
	}
	
	/**
	 * Get vendor media image url
	 * @param string $filename
	 * @param VES_Vendors_Model_Vendor $vendor
	 * @param boolean $size
	 * @return string
	 */
	public function getVendorImageUrl($filename,VES_Vendors_Model_Vendor $vendor,$size=false){
		if(!$size) return Mage::getBaseUrl('media').'ves_vendorsimportproduct/'.$vendor->getVendorId().'/media/'.$filename;
		return Mage::helper('vendorsimport/image')->init('ves_vendorsimportproduct/'.$vendor->getVendorId().'/media/'.$filename,$vendor)->resize($size).'';
	}
	
	/**
	 * Get thumbnail size
	 */
	public function getThumbnailSize(){
		return Mage::getStoreConfig('vendors/vendorsimport/thumbnail_size');
	}
	
	/**
	 * 
	 * Get vendor import folder
	 * @param VES_Vendors_Model_Vendor $vendor
	 * @return string
	 */
	public function getVendorImportFolder(VES_Vendors_Model_Vendor $vendor){
		return Mage::getBaseDir('media').DS.'ves_vendorsimportproduct'.DS.$vendor->getVendorId().DS.'import'.DS;
	}
	
	/**
	 * 
	 * Get csv import file path
	 * @param string $filename
	 * @param VES_Vendors_Model_Vendor $vendor
	 * @return string
	 */
	public function getVendorImportFile($filename,VES_Vendors_Model_Vendor $vendor){
		return Mage::getBaseDir('media').DS.'ves_vendorsimportproduct'.DS.$vendor->getVendorId().DS.'import'.DS.$filename;
	}
	
	/**
	 * Get vendor csv import file url
	 * @param string $filename
	 * @param VES_Vendors_Model_Vendor $vendor
	 * @param boolean $size
	 * @return string
	 */
	public function getVendorImportFileUrl($filename,VES_Vendors_Model_Vendor $vendor){
		return Mage::getBaseUrl('media').'ves_vendorsimportproduct/'.$vendor->getVendorId().'/import/'.$filename;
	}
	
	/**
	 * 
	 * Get vendor import folder
	 * @param VES_Vendors_Model_Vendor $vendor
	 * @return string
	 */
	public function getVendorExportFolder(VES_Vendors_Model_Vendor $vendor){
		return Mage::getBaseDir('media').DS.'ves_vendorsimportproduct'.DS.$vendor->getVendorId().DS.'export'.DS;
	}
	
	/**
	 * 
	 * Get csv import file path
	 * @param string $filename
	 * @param VES_Vendors_Model_Vendor $vendor
	 * @return string
	 */
	public function getVendorExportFile($filename,VES_Vendors_Model_Vendor $vendor){
		return Mage::getBaseDir('media').DS.'ves_vendorsimportproduct'.DS.$vendor->getVendorId().DS.'export'.DS.$filename;
	}
	
	/**
	 * Get vendor csv import file url
	 * @param string $filename
	 * @param VES_Vendors_Model_Vendor $vendor
	 * @param boolean $size
	 * @return string
	 */
	public function getVendorExportFileUrl($filename,VES_Vendors_Model_Vendor $vendor){
		return Mage::getBaseUrl('media').'ves_vendorsimportproduct/'.$vendor->getVendorId().'/export/'.$filename;
	}
}