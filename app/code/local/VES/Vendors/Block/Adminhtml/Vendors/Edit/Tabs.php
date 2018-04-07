<?php

class VES_Vendors_Block_Adminhtml_Vendors_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct(){
		parent::__construct();
		$this->setId('vendors_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('vendors')->__('Vendor Information'));
	}
	
	protected function _beforeToHtml(){
		Mage::dispatchEvent('ves_vendors_prepare_tabs_before',array('tabs'=>$this));
		$this->addTab('main_section', array(
			'label'     => Mage::helper('vendors')->__('Main'),
			'title'     => Mage::helper('vendors')->__('Main'),
			'content'   => $this->getLayout()->createBlock('vendors/adminhtml_vendors_edit_tab_main')->toHtml(),
		));
	
		$this->addTab('info_section', array(
			'label'     => Mage::helper('vendors')->__('Information'),
			'title'     => Mage::helper('vendors')->__('Information'),
			'content'   => $this->getLayout()->createBlock('vendors/adminhtml_vendors_edit_tab_info')->toHtml(),
		));
		Mage::dispatchEvent('ves_vendors_prepare_tabs_after',array('tabs'=>$this));
		return parent::_beforeToHtml();
	}
}