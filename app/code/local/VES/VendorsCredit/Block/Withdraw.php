<?php
class VES_VendorsCredit_Block_Withdraw extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'withdraw';
    $this->_blockGroup = 'vendorscredit';
    $this->_headerText = Mage::helper('vendorscredit')->__('Withdrawal History');
    parent::__construct();
    $this->_removeButton('add');
  }
}