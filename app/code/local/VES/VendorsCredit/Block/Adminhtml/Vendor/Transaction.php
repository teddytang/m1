<?php
class VES_VendorsCredit_Block_Adminhtml_Vendor_Transaction extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct(){
		$this->_controller = 'adminhtml_vendor_transaction';
		$this->_blockGroup = 'vendorscredit';
		$this->_headerText = Mage::helper('vendorscredit')->__('Transaction Manager');
		$this->_addButtonLabel = Mage::helper('vendorscredit')->__('New Transaction');
		parent::__construct();
	}
}