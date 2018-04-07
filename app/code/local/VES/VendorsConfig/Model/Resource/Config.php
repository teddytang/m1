<?php

class VES_VendorsConfig_Model_Resource_Config extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
    {    
        $this->_init('vendorsconfig/config', 'config_id');
    }
    /**
     * Get vendor configuration data
     * @param string $path
     * @param int $vendorId
     */
    public function getConfigData($path, $vendorId){
    	$adapter = $this->_getReadAdapter();
        $bind    = array('path' => $path,'vendor_id'=>$vendorId);
        $select  = $adapter->select()
            ->from($this->getMainTable(), array('value'))
            ->where('path = :path')
            ->where('vendor_id = :vendor_id');
        $result = $adapter->fetchOne($select, $bind);
        return $result;
    }
    
    public function saveConfigData($data,$vendorId){
    	foreach($data as $section=>$sectionData){
    		foreach($sectionData as $group=>$groupData){
    			foreach($groupData as $field=>$value){
    				$path = $section.'/'.$group.'/'.$field;
    				$adapter = $this->_getReadAdapter();
			        $bind    = array('path' => $path,'vendor_id'=>$vendorId);
			        $select  = $adapter->select()
			            ->from($this->getMainTable(), array('config_id'))
			            ->where('path = :path')
			            ->where('vendor_id = :vendor_id');
			        $configId = $adapter->fetchOne($select, $bind);
			        /*Process the Value*/
			        $configXml = Mage::getModel('vendorsconfig/config_base');
			        $configXml->loadVendorConfig();
			        $fieldData = $configXml->getNode('sections/'.$section.'/groups/'.$group.'/fields/'.$field)->asArray();
	    			if (isset($fieldData['backend_model']) && $fieldData['backend_model']) {
	                    $model = Mage::getModel((string)$fieldData['backend_model']);
	                    if (!$model instanceof Mage_Core_Model_Config_Data) {
	                        Mage::throwException('Invalid config field backend model: '.(string)$element->backend_model);
	                    }
	                    $model->setPath($path)
	                        ->setValue($value)
	                        ->beforeSave();
	                    $value = $model->getValue();
	                }
			        if($configId){
			        	/*If the config is exist update the value*/
			        	$config = Mage::getModel('vendorsconfig/config')->load($configId);
			        	$config->setValue($value)->save();
			        }else{
			        	/*If the config is not exist add new row*/
			        	$config = Mage::getModel('vendorsconfig/config')
			        	->setData(array(
			        		'path' 		=> $path,
			        		'vendor_id'	=> $vendorId,
			        		'value'		=> $value,
			        	))->save();
			        }
    			}
    		}
    	}
    }
}