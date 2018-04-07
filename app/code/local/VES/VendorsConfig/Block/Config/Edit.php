<?php

class VES_VendorsConfig_Block_Config_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'vendorsconfig';
        $this->_controller = 'config';
        
        $this->_updateButton('save', 'label', Mage::helper('vendorsconfig')->__('Save Config'));
        $this->_removeButton('delete');
        $this->_removeButton('reset');
        $this->_removeButton('back');
    }

    public function getHeaderText()
    {
        return Mage::helper('vendorsconfig')->__('Configuration');
    }
}