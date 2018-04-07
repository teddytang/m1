<?php

class VES_VendorsCredit_Block_Adminhtml_Payment_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('vendors_form', array('legend'=>Mage::helper('vendorscredit')->__('Method information')));
     
      $fieldset->addField('name', 'text', array(
          'label'     => Mage::helper('vendorscredit')->__('Name'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'name',
      ));
	  $fieldset->addField('fee', 'text', array(
          'label'     => Mage::helper('vendorscredit')->__('Fee'),
          'class'     => 'required-entry validate-number',
          'required'  => true,
          'name'      => 'fee',
      ));
      $fieldset->addField('min', 'text', array(
          'label'     => Mage::helper('vendorscredit')->__('Min amount'),
          'class'     => 'validate-number validate-zero-or-greater',
          'name'      => 'min',
      	  'note'	  => Mage::helper('vendorscredit')->__('Minimum amount to allow vendor withdraw funds. Leave 0 or blank for unlimited.'),
      ));
      $fieldset->addField('max', 'text', array(
          'label'     => Mage::helper('vendorscredit')->__('Max amount'),
          'class'     => 'validate-number validate-zero-or-greater',
          'name'      => 'max',
      	  'note'	  => Mage::helper('vendorscredit')->__('Maximum amount to allow vendor withdraw funds. Leave 0 or blank for unlimited.'),
      ));
      $fieldset->addField('sort_order', 'text', array(
          'label'     => Mage::helper('vendorscredit')->__('Sort Order'),
          'class'     => 'validate-number',
          'required'  => false,
          'name'      => 'sort_order',
      ));
      $fieldset->addField('description', 'textarea', array(
          'label'     => Mage::helper('vendorscredit')->__('Description'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'description',
      ));
      $fieldset->addField('note', 'textarea', array(
          'label'     => Mage::helper('vendorscredit')->__('Note message'),
          'name'      => 'note',
      	  'note'	  => Mage::helper('vendorscredit')->__('This note message will be display on the withdrawal form when vendor submit the withdrawal request.'),
      ));
      $fieldset->addField('additional_info_allow_email_account', 'select', array(
          'label'     => Mage::helper('vendorscredit')->__('Display Email account Text Field'),
          'name'      => 'additional_info[allow_email_account]',
          'values'    => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
      ));
      $fieldset->addField('additional_info_allow_additional_textarea', 'select', array(
          'label'     => Mage::helper('vendorscredit')->__('Display Additional Textarea'),
          'name'      => 'additional_info[allow_additional_textarea]',
          'values'    => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
      ));
      
      if ( Mage::getSingleton('adminhtml/session')->getVendorsData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getVendorsData());
          Mage::getSingleton('adminhtml/session')->setVendorsData(null);
      } elseif ( Mage::registry('method_data') ) {
          $form->setValues(Mage::registry('method_data')->getData());
          $additionalData = unserialize(Mage::registry('method_data')->getAdditionalInfo());
          foreach($additionalData as $key=>$value){
          	$element = $form->getElement('additional_info_'.$key);
          	if($element) $element->setValue($value);
          }
      }
      return parent::_prepareForm();
  }
}