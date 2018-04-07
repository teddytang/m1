<?php
include "app/code/core/Mage/Adminhtml/controllers/System/Convert/GuiController.php";
class VES_VendorsImportProduct_Adminhtml_System_Convert_Profile_MarketplaceController extends Mage_Adminhtml_System_Convert_GuiController
{
	protected function _initProfile($idFieldName = 'id')
    {
        $this->_title($this->__('System'))
             ->_title($this->__('Import and Export'))
             ->_title($this->__('Profiles'));

        $profileId = (int) $this->getRequest()->getParam($idFieldName);
        $profile = Mage::getModel('vendorsimport/profile');

        if ($profileId) {
            $profile->load($profileId);
            if (!$profile->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    $this->__('The profile you are trying to save no longer exists'));
                $this->_redirect('*/*');
                return false;
            }
        }

        Mage::register('current_convert_profile', $profile);

        return $this;
    }
    
	public function indexAction(){
		$this->_title($this->__('System'))
             ->_title($this->__('Import and Export'))
             ->_title($this->__('Profiles'));

        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }
        $this->loadLayout();

        /**
         * Set active menu item
         */
        $this->_setActiveMenu('vendors/import');

        /**
         * Append profiles block to content
         */
        $this->_addContent(
            $this->getLayout()->createBlock('vendorsimport/adminhtml_system_convert_marketplace_gui', 'convert_profile')
        );

        /**
         * Add breadcrumb item
         */
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Import/Export'), Mage::helper('adminhtml')->__('Import/Export'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Profiles'), Mage::helper('adminhtml')->__('Profiles'));

        $this->renderLayout();
	}
	
	public function gridAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('vendorsimport/adminhtml_system_convert_marketplace_gui')->toHtml());
    }
	
	/**
     * Profile edit action
     */
    public function editAction()
    {
        $this->_initProfile();
        $this->loadLayout();

        $profile = Mage::registry('current_convert_profile');

        // set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getConvertProfileData(true);

        if (!empty($data)) {
            $profile->addData($data);
        }

        $this->_title($profile->getId() ? $profile->getName() : $this->__('New Profile'));

        $this->_setActiveMenu('vendors/import');


        $this->_addContent(
            $this->getLayout()->createBlock('vendorsimport/adminhtml_system_convert_gui_edit')
        );

        /**
         * Append edit tabs to left block
         */
        $this->_addLeft($this->getLayout()->createBlock('vendorsimport/adminhtml_system_convert_gui_edit_tabs'));

        $this->renderLayout();
    }
    
	/**
     * Save profile action
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            if (!$this->_initProfile('profile_id')) {
                return;
            }
            $profile = Mage::registry('current_convert_profile');

            // Prepare profile saving data
            if (isset($data)) {
                $profile->addData($data);
            }

            try {
            	$profile->setData('is_ves_marketplace',1);
                $profile->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('The profile has been saved.'));
            } catch (Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setConvertProfileData($data);
                $this->getResponse()->setRedirect($this->getUrl('*/*/edit', array('id' => $profile->getId())));
                return;
            }
            if ($this->getRequest()->getParam('continue')) {
                $this->_redirect('*/*/edit', array('id' => $profile->getId()));
            } else {
                $this->_redirect('*/*');
            }
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                $this->__('Invalid POST data (please check post_max_size and upload_max_filesize settings in your php.ini file).')
            );
            $this->_redirect('*/*');
        }
    }
}