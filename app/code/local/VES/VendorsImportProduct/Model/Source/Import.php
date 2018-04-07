<?php

class VES_VendorsImportProduct_Model_Source_Import extends Varien_Object
{

    static public function getOptionArray($vendorId)
    {
    	$options = array();
    	$collection = Mage::getResourceModel('dataflow/profile_collection')
    		->addFieldToFilter('direction','import')
            ->addFieldToFilter('entity_type', array('notnull'=>''))
            ->addFieldToFilter('is_ves_marketplace',1);
    	foreach($collection as $profile){
    		$options[$profile->getId()] = $profile->getName();
    	}
        return $options;
    }
    
	static public function toOptionArray($vendorId)
    {
    	$options = array();
    	$collection = Mage::getResourceModel('dataflow/profile_collection')
    		->addFieldToFilter('direction','import')
            ->addFieldToFilter('entity_type', array('notnull'=>''))
            ->addFieldToFilter('is_ves_marketplace',1);
    	foreach($collection as $profile){
    		$options[] = array('value'=>$profile->getId(),'label'=>$profile->getName());
    	}
        return $options;
    }
}