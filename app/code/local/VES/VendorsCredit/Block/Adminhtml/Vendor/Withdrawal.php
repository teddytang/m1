<?php
class VES_VendorsCredit_Block_Adminhtml_Vendor_Withdrawal extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct(){
		$this->_controller = 'adminhtml_vendor_withdrawal';
		$this->_blockGroup = 'vendorscredit';
		$this->_headerText = Mage::helper('vendorscredit')->__('Withdrawal Manager');
		parent::__construct();
		$this->_removeButton('add');
	}
}