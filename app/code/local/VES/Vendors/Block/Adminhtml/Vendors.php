<?php
class VES_Vendors_Block_Adminhtml_Vendors extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_vendors';
    $this->_blockGroup = 'vendors';
    $this->_headerText = Mage::helper('vendors')->__('Sellers Manager');
    $this->_addButtonLabel = Mage::helper('vendors')->__('Add Seller');
    parent::__construct();
    //$this->removeButton('add');
  }
}