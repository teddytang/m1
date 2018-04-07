<?php
class VES_VendorsConfig_ConfigController extends VES_Vendors_Controller_Action
{
    /**
     * Default vendor account page
     */
	public function indexAction()
    {
    	$this->loadLayout()->_title(Mage::helper('vendorsconfig')->__('Configuration'));
    	$this->_addBreadcrumb(Mage::helper('vendorsconfig')->__('Configuration'), Mage::helper('vendorsconfig')->__('Configuration'));
    	$this->renderLayout();
    }
    
    public function saveAction(){
    	$data = $this->getRequest()->getPost('config');
		if (isset($_FILES['config']['name']) && is_array($_FILES['config']['name'])) {
    	    /**
    	     * Carefully merge $_FILES and $_POST information
    	     * None of '+=' or 'array_merge_recursive' can do this correct
    	     */
    	    foreach($_FILES['config']['name'] as $groupName => $group) {
    	        if (is_array($group)) {
    	            foreach ($group as $fieldSet => $fields) {
    	                //echo $fieldName."<br />";
    	                if (!empty($fields)) {
    	                    foreach($fields as $fieldName=>$field){
    	                       $data[$groupName][$fieldSet][$fieldName] = $field;
    	                    }
    	                }
    	            }
    	        }
    	    }
    	}
    	try{
	    	$vendorId = $this->_getSession()->getVendorId();
	    	Mage::getResourceModel('vendorsconfig/config')->saveConfigData($data,$vendorId);
	    	$this->_getSession()->addSuccess(Mage::helper('vendorsconfig')->__('The data has been saved.'));
    	}catch (Mage_Core_Exception $e){
    		$this->_getSession()->addError($e->getMessage());
    	}catch (Exception $e){
    		$this->_getSession()->addError($e->getMessage());
    	}
    	$this->_redirect('*/*/',array('section'=>$this->getRequest()->getParam('section',false)));
    }
    
}