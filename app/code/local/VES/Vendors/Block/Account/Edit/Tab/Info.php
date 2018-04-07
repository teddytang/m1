<?php

class VES_Vendors_Block_Account_Edit_Tab_Info extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('vendors_form', array('legend'=>Mage::helper('vendors')->__('Item information')));
     
      $fieldset->addField('name', 'text', array(
      		'label'     => Mage::helper('vendors')->__('Vendor Name'),
      		'class'     => 'required-entry',
      		'required'  => true,
      		'name'      => 'name',
      ));

      $fieldset->addField('website', 'text', array(
      		'label'     => Mage::helper('vendors')->__('Website'),
      		'class'     => 'required-entry',
      		'required'  => true,
      		'name'      => 'website',
      ));
      $fieldset->addField('logo', 'image', array(
      		'label'     => Mage::helper('vendors')->__('Logo'),
      		'class'     => 'required-entry',
      		'required'  => false,
      		'name'      => 'logo',
      ));
      $fieldset->addField('address', 'text', array(
      		'label'     => Mage::helper('vendors')->__('Address'),
      		'class'     => 'required-entry',
      		'required'  => true,
      		'name'      => 'address',
      ));
      $fieldset->addField('city', 'text', array(
      		'label'     => Mage::helper('vendors')->__('City'),
      		'class'     => 'required-entry',
      		'required'  => true,
      		'name'      => 'name',
      ));
      $fieldset->addField('country', 'select', array(
      		'label'     => Mage::helper('vendors')->__('Country'),
      		'class'     => 'required-entry',
      		'required'  => true,
      		'name'      => 'country',
      		'values'	=> Mage::getModel('adminhtml/system_config_source_country_full')->toOptionArray()
      ));
      
		
      /*$fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('vendors')->__('Status'),
          'name'      => 'status',
          'values'    => Mage::getModel('vendors/status')->getAllOptions(),
      ));
     */
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