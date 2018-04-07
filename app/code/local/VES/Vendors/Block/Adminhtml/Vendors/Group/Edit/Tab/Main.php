<?php

class VES_Vendors_Block_Adminhtml_Vendors_Group_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('vendors_form', array('legend'=>Mage::helper('vendors')->__('Vendor information')));
     
      $fieldset->addField('vendor_group_code', 'text', array(
          'label'     => Mage::helper('vendors')->__('Group Name'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'vendor_group_code',
      ));
	  /*
      $fieldset->addField('fee_by', 'select', array(
          'label'     => Mage::helper('vendors')->__('Calculate Fee By'),
          'name'      => 'fee_by',
      	  'required'  => true,
          'values'    => Mage::getModel('vendors/source_feetype')->getAllOptions(),
      ));
      
      $fieldset->addField('fee', 'text', array(
          'label'     => Mage::helper('vendors')->__('Fee'),
      	  'style'	  => 'validate-number',
          'name'      => 'fee',
          'required'  => true,
      	  'note'	  => Mage::helper('vendors')->__('This fee will be deducted for each sale of vendor'),
      ));
      */
      if ( Mage::getSingleton('adminhtml/session')->getVendorsData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getVendorsData());
          Mage::getSingleton('adminhtml/session')->setVendorsData(null);
      } elseif ( Mage::registry('group_data') ) {
          $form->setValues(Mage::registry('group_data')->getData());
      }
      return parent::_prepareForm();
  }
}