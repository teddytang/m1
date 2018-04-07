<?php
class VES_Vendors_AccountController extends VES_Vendors_Controller_Action
{
    /**
     * Default vendor account page
     */
	public function indexAction()
    {
    	$this->loadLayout()->_title($this->__('Account'));
    	$this->_addBreadcrumb(Mage::helper('vendors')->__('My Account'), Mage::helper('vendors')->__('My Account'));
    	$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
		Mage::register('vendors_data', $this->_getSession()->getVendor());
		$this->_addContent($this->getLayout()->createBlock('vendors/account_edit'))
				->_addLeft($this->getLayout()->createBlock('vendors/account_edit_tabs'));
    	$this->renderLayout();
    }
    
    public function saveAction(){
    	$data = $this->getRequest()->getParams();
    	if(isset($_FILES['logo']['name']) && $_FILES['logo']['name'] != '') {
			try {	
				/* Starting upload */	
				$uploader = new Varien_File_Uploader('logo');
           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
				$uploader->setAllowRenameFiles(true);
				$uploader->setFilesDispersion(true);
				$path = Mage::getBaseDir('media') . DS."ves_vendors".DS."logo".DS ;
				$uploader->save($path, $_FILES['logo']['name']);
				$data['logo'] = "ves_vendors/logo".$uploader->getUploadedFileName();
			} catch (Exception $e) {
	      		
	        }
		}else{
			if(isset($data['logo']['delete']) && $data['logo']['delete']){
				$data['logo'] = '';
			}else{
				$data['logo'] = $data['logo']['value'];
			}
		}
		$vendor = Mage::getModel('vendors/vendor')->load(Mage::getSingleton('vendors/session')->getVendorId());
		
    	if($data['new_password']){
			$data['password'] = $data['confirmation'] = $data['new_password'];
		}
		
		foreach($data as $key=>$value)$vendor->setData($key,$value);
		try{
			$vendor->setUpdatedAt(now());
			$vendor->save();
			Mage::getSingleton('vendors/session')->addSuccess(Mage::helper('vendors')->__('Your account information was successfully saved'));
			Mage::getSingleton('vendors/session')->setFormData(false);
		}catch (Exception $e) {
        	Mage::getSingleton('vendors/session')->addError($e->getMessage());
        	Mage::getSingleton('vendors/session')->setFormData($data);
        	$this->_redirect('*/*/');
        	return;
        }
    	$this->_redirect('*/*');
    }
}