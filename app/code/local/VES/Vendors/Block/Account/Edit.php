<?php

class VES_Vendors_Block_Account_Edit extends VES_Vendors_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'vendors';
        $this->_controller = 'account';
        
        $this->_updateButton('save', 'label', Mage::helper('vendors')->__('Save'));
        $this->removeButton('delete');
        //$this->_updateButton('delete', 'label', Mage::helper('vendors')->__('Delete Vendor'));
    }

    public function getHeaderText()
    {
        if( Mage::registry('vendors_data') && Mage::registry('vendors_data')->getId() ) {
            return Mage::helper('vendors')->__("Vendor Account Information", $this->htmlEscape(Mage::registry('vendors_data')->getVendorId()));
        } else {
            return Mage::helper('vendors')->__('Add Vendor');
        }
    }
	/**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/dashboard');
    }
}