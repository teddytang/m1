<?php

class VES_VendorsCredit_Block_Withdraw_Review_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('vendors_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('vendorscredit')->__('Withdraw Funds'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('main_section', array(
          'label'     => Mage::helper('vendorscredit')->__('Main'),
          'title'     => Mage::helper('vendorscredit')->__('Main'),
          'content'   => $this->getLayout()->createBlock('vendorscredit/withdraw_review_edit_tab_main')->toHtml(),
      ));
      return parent::_beforeToHtml();
  }
}