<?php
class VES_VendorsCredit_Block_Adminhtml_Vendor_Transaction_Escrow extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct(){
		$this->_controller = 'adminhtml_vendor_transaction_escrow';
		$this->_blockGroup = 'vendorscredit';
		$this->_headerText = Mage::helper('vendorscredit')->__('Escrow Transactions');
		parent::__construct();
		$this->_removeButton('add');
		return $this;
	}
}