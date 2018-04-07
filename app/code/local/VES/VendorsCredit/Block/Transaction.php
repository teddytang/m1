<?php
class VES_VendorsCredit_Block_Transaction extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'transaction';
    $this->_blockGroup = 'vendorscredit';
    $this->_headerText = Mage::helper('vendorscredit')->__('Credit Transactions');
    $this->_addButtonLabel = Mage::helper('vendorscredit')->__('New Transaction');
    parent::__construct();
    $this->_removeButton('add');
  }
}