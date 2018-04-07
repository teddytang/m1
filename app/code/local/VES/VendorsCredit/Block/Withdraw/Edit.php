<?php

class VES_VendorsCredit_Block_Withdraw_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'vendorscredit';
        $this->_controller = 'withdraw';
        $this->_removeButton('delete');
        $this->_removeButton('reset');
        $this->_removeButton('save');
    }

    public function getHeaderText()
    {
    	return Mage::helper('vendorscredit')->__('Withdraw Funds');
    }
    public function getBackUrl(){
    	return $this->getUrl('vendors');
    }
}