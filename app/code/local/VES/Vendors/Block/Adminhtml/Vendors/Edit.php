<?php

class VES_Vendors_Block_Adminhtml_Vendors_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'vendors';
        $this->_controller = 'adminhtml_vendors';
        
        $this->_updateButton('save', 'label', Mage::helper('vendors')->__('Save Vendor'));
        $this->_updateButton('delete', 'label', Mage::helper('vendors')->__('Delete Vendor'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('vendors_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'vendors_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'vendors_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('vendors_data') && Mage::registry('vendors_data')->getId() ) {
            return Mage::helper('vendors')->__("Edit Vendor '%s'", $this->htmlEscape(Mage::registry('vendors_data')->getVendorId()));
        } else {
            return Mage::helper('vendors')->__('Add Vendor');
        }
    }
}