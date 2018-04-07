<?php

class VES_VendorsCredit_Block_Withdraw_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('vendors_form', array('legend'=>Mage::helper('vendorscredit')->__('Withdrawal Request')));
      
      $fieldset->addType('withdraw_payment','VES_VendorsCredit_Block_Form_Element_Payment');
      $fieldset->addField('payment_header', 'withdraw_payment', array(
	          'class'     => 'required-entry',
	          'required'  => true,
	          'name'      => 'payment_method',
      		  'header'	  => true,
	      	  'payment_method'	  	=> Mage::helper('vendorscredit')->__('Payment Method'),
	      	  'payment_description'	=> Mage::helper('vendorscredit')->__('Description'),
	      	  'payment_fee'			=> Mage::helper('vendorscredit')->__('Fee'),
	      ));
      foreach(Mage::getModel('vendorscredit/payment')->getCollection() as $payment){
	      $fieldset->addField('payment_'.$payment->getId(), 'withdraw_payment', array(
	          'label'     => $payment->getName(),
	          'class'     => 'required-entry',
	          'required'  => true,
	          'name'      => 'payment_method',
	      	  'fee_type'	  		=> 'currency',
	      	  'method_id'			=> $payment->getId(),
	      	  'payment_method'	  	=> $payment->getName(),
	      	  'payment_description'	=> $payment->getDescription(),
	      	  'payment_fee'			=> $payment->getFee(),
	      ));
      }
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