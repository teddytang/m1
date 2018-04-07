<?php

class VES_VendorsCredit_Block_Adminhtml_Payment_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'vendorscredit';
        $this->_controller = 'adminhtml_payment';
        
        $this->_updateButton('save', 'label', Mage::helper('vendorscredit')->__('Save Method'));
        $this->_updateButton('delete', 'label', Mage::helper('vendorscredit')->__('Delete Method'));
		
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
        if( Mage::registry('method_data') && Mage::registry('method_data')->getId() ) {
            return Mage::helper('vendorscredit')->__("Edit Method '%s'", $this->htmlEscape(Mage::registry('method_data')->getId()));
        } else {
            return Mage::helper('vendorscredit')->__('Add Method');
        }
    }
}