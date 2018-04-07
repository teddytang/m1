<?php

class VES_VendorsImportProduct_Model_Observer
{
	/**
	 *
	 * Hide the menu if the module is not enabled
	 * @param Varien_Event_Observer $observer
	 */
	public function ves_vendor_menu_check_acl(Varien_Event_Observer $observer){
		$resource 	= $observer->getResource();
    	$result 	= $observer->getResult();
    	
    	if($resource == 'vendors/importeproduct' && !Mage::helper('vendorsimport')->moduleEnable()){
    		$result->setIsAllowed(false);
    	}
	}
	
    /**
     * Remove message toplink if sub vendor account do not have permission.
     * @param Varien_Event_Observer $observer
     */
    public function ves_vendorsmessage_module_enable(Varien_Event_Observer $observer){
    	/*Vendor CP*/
    	$session = Mage::getSingleton('vendors/session');
    	if(!$session->getIsSubAccount()) return;
    	
		if($account = $session->getSubAccount()){
			$resources = $account->getRole()->getRoleResources();
			if(!in_array('message',$resources)){
				$result = $observer->getEvent()->getResult();
				$result->setData('module_enable',false);
			}
		}
    }
}