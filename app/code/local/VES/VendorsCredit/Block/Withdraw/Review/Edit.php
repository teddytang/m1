<?php

class VES_VendorsCredit_Block_Withdraw_Review_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'vendorscredit';
        $this->_controller = 'withdraw_review';
        $this->_removeButton('delete');
        $this->_removeButton('reset');
        $this->_updateButton('save', 'label', Mage::helper('vendorscredit')->__('Submit'));
    }

    public function getHeaderText()
    {
    	return Mage::helper('vendorscredit')->__('Withdraw Funds');
    }
    public function getBackUrl(){
    	return $this->getUrl('vendors/credit_withdraw/form',array('method'=>Mage::registry('payment_method')->getId()));
    }
}