<?php

class VES_VendorsImportProduct_Model_Source_Attributes
{

    static public function getOptionArray()
    {
    	$options = array();
    	$collection = Mage::getResourceModel('catalog/product_attribute_collection');;
    	foreach($collection as $attr){
    		$options[$attr->getAttributeCode()] = $attr->getAttributeCode();
    	}
        return $options;
    }
    
	static public function toOptionArray()
    {
    	$options = array();
    	$collection = Mage::getResourceModel('catalog/product_attribute_collection');;

    	foreach($collection as $attr){
    		$options[] = array('value'=>$attr->getAttributeCode(),'label'=>$attr->getAttributeCode());
    	}
        return $options;
    }
}