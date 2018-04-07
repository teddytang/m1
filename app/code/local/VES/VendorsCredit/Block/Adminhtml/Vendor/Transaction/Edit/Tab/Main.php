<?php

class VES_VendorsCredit_Block_Adminhtml_Vendor_Transaction_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('vendors_form', array('legend'=>Mage::helper('vendorscredit')->__('Method information')));
      
      $transactionTypes = array(
      	'add_credit' 	=> Mage::helper('vendorscredit')->__('Add Credit'),
      	'deduct_credit' => Mage::helper('vendorscredit')->__('Deduct Credit'),
      );
      $fieldset->addField('vendor_id', 'select', array(
          'label'     => Mage::helper('vendorscredit')->__('Vendor'),
          'class'     => 'required-entry',
      	  'options'	  => Mage::getModel('vendorscredit/source_vendor')->getAllOptions(),
          'required'  => true,
          'name'      => 'vendor_id',
      ));
      
      $fieldset->addField('type', 'select', array(
          'label'     => Mage::helper('vendorscredit')->__('Transaction Type'),
      	  'options'	  => $transactionTypes,
          'required'  => true,
          'name'      => 'type',
      ));
	  $fieldset->addField('amount', 'text', array(
          'label'     => Mage::helper('vendorscredit')->__('Amount'),
          'class'     => 'required-entry validate-number',
          'required'  => true,
          'name'      => 'amount',
      ));
      
      $fieldset->addField('description', 'textarea', array(
          'label'     => Mage::helper('vendorscredit')->__('Note'),
          'required'  => false,
          'name'      => 'description',
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