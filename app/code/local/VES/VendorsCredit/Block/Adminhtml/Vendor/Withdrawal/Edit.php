<?php

class VES_VendorsCredit_Block_Adminhtml_Vendor_Withdrawal_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'vendorscredit';
        $this->_controller = 'adminhtml_vendor_withdrawal';
        
        $this->_updateButton('save', 'label', Mage::helper('vendorscredit')->__('Complete Withdrawal'));
        $this->_removeButton('reset');
        
        if(Mage::registry('withdrawal_data')->getStatus() != VES_VendorsCredit_Model_Withdrawal::STATUS_PENDING){
        	$this->_removeButton('save');
        	$this->_removeButton('delete');
        }else{
        	$this->_updateButton('delete', 'label', Mage::helper('vendorscredit')->__('Reject'));
        	$this->_updateButton('delete', 'onclick', 'rejectWithdrawal()');
        	 $this->_formScripts[] = "
	            function rejectWithdrawal(){
	            	if(confirm('".Mage::helper('adminhtml')->__('Are you sure you want to do this?')."')){
	                	editForm.submit($('edit_form').action+'status/reject/');
	                }
	            }
	        ";
        }
    }

    public function getHeaderText()
    {
    	return Mage::helper('vendorscredit')->__('Withdrawal Information');
    }
}