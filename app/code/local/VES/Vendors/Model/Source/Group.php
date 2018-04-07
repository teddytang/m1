<?php

class VES_Vendors_Model_Source_Group extends Varien_Object
{
    static public function getOptionArray()
    {
    	$result = array();
    	foreach(Mage::getModel('vendors/group')->getCollection() as $group){
    		$result[$group->getId()] = $group->getName();
    	}
    	return $result;
    }
	static public function toOptionArray()
    {
    	$result = array(array('label'=> Mage::helper('vendors')->__('-- Please select --'),'value'=>''));
    	foreach(Mage::getModel('vendors/group')->getCollection() as $group){
    		$result[] = array(
    			'label'	=> $group->getName(),
    			'value' => $group->getId(),
    		);
    	}
    	return $result;
    }
	public function getAllOptions()
    {
    	$result = array();
    	foreach(Mage::getModel('vendors/group')->getCollection() as $group){
    		$result[] = array(
    			'label'	=> $group->getName(),
    			'value' => $group->getId(),
    		);
    	}
    	return $result;
    }
}