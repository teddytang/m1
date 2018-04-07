<?php

class VES_VendorsGroup_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
	 * Get all group options
	 */
	public function getSections(){
		return Mage::app()->getConfig()->getNode('vendors/group_options')->asArray();
	}
	
	/**
	 * Get config by resource id and group ID
	 * @param string $resourceId
	 * @param string $groupId
	 */
	public function getConfig($resourceId, $groupId){
		return Mage::getResourceModel('vendorsgroup/rule')->getConfig($resourceId, $groupId);
	}
}