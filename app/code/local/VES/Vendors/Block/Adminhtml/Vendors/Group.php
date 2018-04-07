<?php
class VES_Vendors_Block_Adminhtml_Vendors_Group extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_vendors_group';
    $this->_blockGroup = 'vendors';
    $this->_headerText = Mage::helper('vendors')->__('Groups Manager');
    $this->_addButtonLabel = Mage::helper('vendors')->__('Add Group');
    parent::__construct();
  }
}