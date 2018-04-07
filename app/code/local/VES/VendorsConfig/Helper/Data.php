<?php

class VES_VendorsConfig_Helper_Data extends Mage_Core_Helper_Abstract
{
	protected $_config;
	public function getConfig(){
		if(!$this->_config){
			$config = new VES_VendorsConfig_Model_Config_Base();
			$config->loadVendorConfig();
			$this->_config = $config;
		}
		
		return $this->_config;
	}
	
	public function getConfigUrl(){
		return Mage::getUrl('vendors/config');
	}

	/**
	 * Get vendor configuration by path
	 * @param string $path
	 * @param int $vendorId
	 */
	public function getVendorConfig($path,$vendorId){
		$result = Mage::getResourceModel('vendorsconfig/config')->getConfigData($path,$vendorId);
		if($result === false){
			$defaultConfigs = Mage::getConfig()->getNode('vendor_config')->asArray();
			$path = explode("/", $path);
			if(isset($path[0]) && isset($path[1]) && isset($path[2]) && isset($defaultConfigs[$path[0]][$path[1]][$path[2]])){
				return $defaultConfigs[$path[0]][$path[1]][$path[2]];
			}
		}
		return $result;
	}
}