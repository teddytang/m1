<?php

class VES_VendorsCredit_Block_Withdraw_View_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('withdrawal_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('vendors')->__('Withdrawal Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('main_section', array(
          'label'     => Mage::helper('vendors')->__('Withdrawal Information'),
          'title'     => Mage::helper('vendors')->__('Withdrawal Information'),
          'content'   => $this->getLayout()->createBlock('vendorscredit/withdraw_view_tab_main')->toHtml(),
      ));
      return parent::_beforeToHtml();
  }
}