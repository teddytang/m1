<?php

class VES_VendorsCredit_Block_Adminhtml_Vendor_Withdrawal_Edit_Tab_Main extends VES_VendorsCredit_Block_Withdraw_View_Tab_Main
{
	protected function _prepareForm()
	{
		parent::_prepareForm();
		$form 	= $this->getForm();
		$fieldset1 = $form->addFieldset('vendorinfo_form', array('legend'=>Mage::helper('vendorscredit')->__('Vendor Information')),'^');
		$fieldset1->addType('amount','VES_VendorsCredit_Block_Form_Element_Withdrawal_Currency');
		$fieldset1->addField('credit', 'amount', array(
			'label'     => Mage::helper('vendorscredit')->__('Credit Balance'),
			'name'      => 'credit',
		));
		
		$fieldset = $form->getElement('withdrawal_form');
		$fieldset->removeField('note');
		$noteType = (Mage::registry('withdrawal_data')->getStatus() == VES_VendorsCredit_Model_Withdrawal::STATUS_PENDING)?'textarea':'custom_note';
		$fieldset->addField('note', $noteType, array(
			'label'     => Mage::helper('vendorscredit')->__('Note'),
			'required'  => false,
			'name'      => 'note',
		));
		if ( Mage::getSingleton('adminhtml/session')->getVendorsData() ){
			$form->setValues(Mage::getSingleton('adminhtml/session')->getVendorsData());
			Mage::getSingleton('adminhtml/session')->setVendorsData(null);
		} elseif ( Mage::registry('withdrawal_data') ) {
			$form->setValues(Mage::registry('withdrawal_data')->getData());
			$form->getElement('credit')->setValue(Mage::registry('withdrawal_data')->getVendor()->getCredit());
		}
		return $this;
	}
}