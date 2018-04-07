<?php
class VES_VendorsCredit_Block_Adminhtml_Payment extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_payment';
    $this->_blockGroup = 'vendorscredit';
    $this->_headerText = Mage::helper('vendorscredit')->__('Payment Method Manager');
    $this->_addButtonLabel = Mage::helper('vendorscredit')->__('New Method');
    parent::__construct();
  }
}