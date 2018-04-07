<?php

class VES_VendorsConfig_Model_Config_Base extends Varien_Simplexml_Config
{
	 const CACHE_TAG         = 'CONFIG';
	/**
     * Constructor
     *
     */
    public function __construct($sourceData=null)
    {
    	//$this->setCacheId('config_vendor');
        $this->_elementClass = 'Mage_Core_Model_Config_Element';
        parent::__construct($sourceData);
    }
    
    public function loadVendorConfig(){
    	$cacheId = 'vendor_config_fields';
    	if(!Mage::app()->useCache('config') || !$this->_loadCache($cacheId)){
	    	$config = Mage::app()->getConfig();
	        $this->loadString('<config/>');
	        $fileConfig = new VES_VendorsConfig_Model_Config_Base();
	        
	    	foreach($config->getModuleConfig()->asArray() as $moduleName=>$module){
	    		if(!isset($module['active']) || $module['active'] != 'true') continue;
	    		$filename = $config->getModuleDir('etc', $moduleName).DS.'vendor.xml';
	    		$fileConfig->loadFile($filename);
	            $this->extend($fileConfig);
	    	}
	    	Mage::app()->saveCache($this->getXmlString(),$cacheId,array(self::CACHE_TAG),10000);
    	}else{
    		$xml = $this->_loadCache($cacheId);
    		$this->loadString($xml);
    	}
    }
    
    public function getSections(){
    	$sections = $this->getNode('sections');
    	if($sections) return $sections->asArray();
    	return false;
    }
	/**
     * Load cached data by identifier
     *
     * @param   string $id
     * @return  string
     */
    protected function _loadCache($id)
    {
        return Mage::app()->loadCache($id);
    }
    
}