<?php

class VES_VendorsCredit_Block_Withdraw_View extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId 	= 'id';
        $this->_blockGroup 	= 'vendorscredit';
        $this->_controller 	= 'withdraw';
        $this->_mode		= 'view';
        
        $this->_removeButton('reset');
        if(Mage::registry('withdrawal_data')->getStatus() == VES_VendorsCredit_Model_Withdrawal::STATUS_CANCELED){
        	$this->_updateButton('save', 'label', Mage::helper('vendorscredit')->__('Re-Submit Withdrawal'));
        }elseif(Mage::registry('withdrawal_data')->getStatus() == VES_VendorsCredit_Model_Withdrawal::STATUS_PENDING){
        	$this->_updateButton('delete', 'label', Mage::helper('vendorscredit')->__('Cancel Withdrawal'));
        	$this->_removeButton('save');
        }else{
        	$this->_removeButton('save');
        	$this->_removeButton('delete');
        }
    }
	
    public function getBackUrl(){
    	return $this->getUrl('vendors/credit_withdraw/history');
    }
    public function getHeaderText()
    {
    	return Mage::helper('vendorscredit')->__('Withdrawal Information');
    }
}