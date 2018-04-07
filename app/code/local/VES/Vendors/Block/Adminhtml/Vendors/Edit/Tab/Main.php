<?php

class VES_Vendors_Block_Adminhtml_Vendors_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('vendors_form_main', array('legend'=>Mage::helper('vendors')->__('Vendor information')));
		$model = Mage::registry('vendors_data');
		$isEditExistVendor = $model->getId();
		$fieldset->addField('vendor_id', $isEditExistVendor?'label':'text', array(
			'label'     => Mage::helper('vendors')->__('Vendor Id'),
			'class'     => 'required-entry validate-code',
			'required'  => true,
			'name'      => 'vendor_id',
		));

		$fieldset->addField('group_id', 'select', array(
			'label'     => Mage::helper('vendors')->__('Group'),
			'name'      => 'group_id',
			'values'    => Mage::getModel('vendors/source_group')->getAllOptions(),
		));

		$field =$fieldset->addField('website_id', 'select', array(
			'name'      => 'website_id',
			'label'     => Mage::helper('cms')->__('Website'),
			'title'     => Mage::helper('cms')->__('Website'),
			'required'  => true,
			'disabled'	=> $isEditExistVendor,
			'values'    => Mage::getSingleton('adminhtml/system_store')->getWebsiteValuesForForm(true, false),
		));

		$fieldset->addField('status', 'select', array(
			'label'     => Mage::helper('vendors')->__('Status'),
			'name'      => 'status',
			'values'    => Mage::getModel('vendors/source_status')->getAllOptions(),
		));
		Mage::dispatchEvent('ves_vendors_adminhtml_edit_tab_main',array('form'=>$this->getForm(),'fieldset'=>$fieldset));
		
		if ( Mage::getSingleton('adminhtml/session')->getVendorsData() )
		{
			$form->setValues(Mage::getSingleton('adminhtml/session')->getVendorsData());
			Mage::getSingleton('adminhtml/session')->setVendorsData(null);
		} elseif ( Mage::registry('vendors_data') ) {
			$form->setValues(Mage::registry('vendors_data')->getData());
		}
		return parent::_prepareForm();
	}
}