<?php

class VES_VendorsCredit_Block_Adminhtml_Vendor_Transaction_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('transaction_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('vendors')->__('Transaction Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('main_section', array(
          'label'     => Mage::helper('vendors')->__('Transaction Information'),
          'title'     => Mage::helper('vendors')->__('Transaction Information'),
          'content'   => $this->getLayout()->createBlock('vendorscredit/adminhtml_vendor_transaction_edit_tab_main')->toHtml(),
      ));
      return parent::_beforeToHtml();
  }
}