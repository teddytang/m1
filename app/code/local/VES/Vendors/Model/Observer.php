<?php

class VES_Vendors_Model_Observer
{
    
    public function controller_action_layout_load_before(Varien_Event_Observer $observer){
    	if(!Mage::helper('vendors')->moduleEnabled()){
    		return;
    	}
    	
    	/** @var $layout Mage_Core_Model_Layout */
        $layout = $observer->getEvent()->getLayout();
    	$action = $observer->getEvent()->getAction();
    	$moduleName = $action->getRequest()->getModuleName();
    	if($moduleName && $moduleName == 'vendors'){
    		/* Set the area to adminhtml */
    		Mage::getSingleton('core/design_package')->setArea('adminhtml')->setPackageName(Mage_Core_Model_Design_Package::DEFAULT_PACKAGE)->setTheme(Mage_Core_Model_Design_Package::DEFAULT_THEME);
    		$layout->getUpdate()->addHandle(VES_Vendors_Helper_Data::VENDOR_LAYOUT_HANDLE);
    	
	        if ($layout) {
	            $handle = (Mage::getSingleton('vendors/session')->isLoggedIn())
	                ? VES_Vendors_Helper_Data::LOGGED_IN_LAYOUT_HANDLE
	                : VES_Vendors_Helper_Data::LOGGED_OUT_LAYOUT_HANDLE;
	                
	            /* Do not add handle for ajax request */
	            if(!$action->getRequest()->isAjax()){
	            	$layout->getUpdate()->addHandle($handle);
	            }
	        }
    	}
        
    }
	
	public function controller_action_predispatch(Varien_Event_Observer $observer){
    	if (Mage::getSingleton('admin/session')->isLoggedIn()) {
            $feedModel  = Mage::getModel('vendors/feed');
            $feedModel->checkUpdate();
        }
    }
	

	
	/** send email when status vendor is active **/
	public function adminhtml_vendor_save_after(Varien_Event_Observer $observer){
		$vendor =  $observer->getVendor();
		if($vendor->getStatus() == VES_Vendors_Model_Vendor::STATUS_ACTIVATED && !$vendor->getData("is_sendmail_active_vendor")){
			if(Mage::getStoreConfig('vendors/create_account/send_approved')){
				$vendor->sendNewAccountEmail("active");
				$vendor->setData("is_sendmail_active_vendor",1)->save();
			}
		}
		else{
			$vendor->setData("is_sendmail_active_vendor",0)->save();
		}
	}
}