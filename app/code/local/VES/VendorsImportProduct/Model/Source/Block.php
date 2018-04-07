<?php

class VES_VendorsImportProduct_Model_Source_Block extends Varien_Object
{

    static public function getOptionArray($vendorId)
    {
    	$options = array();
    	$collection = Mage::getResourceModel('cms/block_collection');
    	$option[] = Mage::helper('vendorsimport')->__('Do not use');
    	foreach($collection as $block){
    		$options[$block->getId()] = $block->getTitle();
    	}
        return $options;
    }
    
	static public function toOptionArray($vendorId)
    {
    	$options = array();
    	$options[] = array('value'=>'', 'label'=>Mage::helper('vendorsimport')->__('Do not use'));
    	$collection = Mage::getResourceModel('cms/block_collection');
    	foreach($collection as $block){
    		$options[] = array('value'=>$block->getId(),'label'=>$block->getTitle());
    	}
        return $options;
    }
}