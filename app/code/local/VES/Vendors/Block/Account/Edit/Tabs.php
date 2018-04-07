<?php

class VES_Vendors_Block_Account_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('vendors_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('vendors')->__('Vendor Information'));
	}
	
	protected function _beforeToHtml()
	{
		$this->addTab('main_section', array(
			'label'     => Mage::helper('vendors')->__('Main'),
			'title'     => Mage::helper('vendors')->__('Main'),
			'content'   => $this->getLayout()->createBlock('vendors/account_edit_tab_main')->toHtml(),
		));
		$this->setActiveTab('main_section');
		return parent::_beforeToHtml();
	}

}