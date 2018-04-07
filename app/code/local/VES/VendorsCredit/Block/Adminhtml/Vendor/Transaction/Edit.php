<?php

class VES_VendorsCredit_Block_Adminhtml_Vendor_Transaction_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'vendorscredit';
        $this->_controller = 'adminhtml_vendor_transaction';
        
        $this->_updateButton('save', 'label', Mage::helper('vendorscredit')->__('Save Transaction'));
        $this->_removeButton('reset');
    }

    public function getHeaderText()
    {
    	return Mage::helper('vendorscredit')->__('Add Transaction');
    }
}