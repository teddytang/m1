<?php

class VES_VendorsGroup_Model_Observer_Message
{	
	/**
	 * Check if the message module is enable
	 * @param Varien_Event_Observer $observer
	 */
	public function ves_vendorsmessage_module_enable(Varien_Event_Observer $observer){
		$result = $observer->getEvent()->getResult();
		/*Vendor Page*/
		if($vendor = Mage::registry('vendor')){
			$groupId = $vendor->getGroupId();
			$messageEnableConfig = Mage::helper('vendorsgroup')->getConfig('message/enabled',$groupId);
			$result->setData('module_enable',$messageEnableConfig);
			return;
		}
		
		/*Vendor CP*/	
		if($vendor = Mage::getSingleton('vendors/session')->getVendor()){
			$groupId = $vendor->getGroupId();
			$messageEnableConfig = Mage::helper('vendorsgroup')->getConfig('message/enabled',$groupId);
			$result->setData('module_enable',$messageEnableConfig);
			return;
		}
	}
}