<?php

class VES_Vendors_Block_Adminhtml_Vendors_Edit_Tab_Info extends VES_Vendors_Block_Account_Edit_Tab_Main
{
	protected function _prepareForm(){
		parent::_prepareForm();
		$fieldSet = $this->getForm()->getElement('vendors_form');
		$fieldSet->removeField('vendor_id');
		$fieldSet->removeField('group_id');
	}
}