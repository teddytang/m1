<?php

class VES_VendorsCredit_Block_Adminhtml_Payment_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('vendors_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('vendors')->__('Method Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('main_section', array(
          'label'     => Mage::helper('vendors')->__('Main'),
          'title'     => Mage::helper('vendors')->__('Main'),
          'content'   => $this->getLayout()->createBlock('vendorscredit/adminhtml_payment_edit_tab_main')->toHtml(),
      ));
      return parent::_beforeToHtml();
  }
}