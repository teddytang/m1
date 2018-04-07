<?php

class VES_Vendors_Block_Adminhtml_Vendors_Group_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('group_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('vendors')->__('Group Information'));
	}
	
	protected function _beforeToHtml()
	{
		Mage::dispatchEvent('ves_vendors_group_prepare_tabs_before',array('tabs'=>$this));
		$this->addTab('main_section', array(
			'label'     => Mage::helper('vendors')->__('Main'),
			'title'     => Mage::helper('vendors')->__('Main'),
			'content'   => $this->getLayout()->createBlock('vendors/adminhtml_vendors_group_edit_tab_main')->toHtml(),
		));
		Mage::dispatchEvent('ves_vendors_group_prepare_tabs_after',array('tabs'=>$this));
		return parent::_beforeToHtml();
	}
}