<?php

class VES_Vendors_Block_Account_Edit_Tab_Config extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      Mage::dispatchEvent('vendor_edit_config_form_before',array('form'=>$form));
      
     Mage::dispatchEvent('vendor_edit_config_form_after',array('form'=>$form));
     
      if ( Mage::getSingleton('adminhtml/session')->getVendorsData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getVendorsData());
          Mage::getSingleton('adminhtml/session')->setVendorsData(null);
      } elseif ( Mage::registry('vendors_data') ) {
          //$form->setValues(Mage::registry('vendors_data')->getData());
          $config = unserialize(Mage::registry('vendors_data')->getConfig());
          foreach ($config as $key => $value){
          	if($form->getElement($key))
          		$form->getElement($key)->setValue($value);
          }
      }
      return parent::_prepareForm();
  }
}