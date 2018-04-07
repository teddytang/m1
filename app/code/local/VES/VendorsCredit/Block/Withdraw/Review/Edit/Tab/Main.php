<?php

class VES_VendorsCredit_Block_Withdraw_Review_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('vendors_form', array('legend'=>Mage::helper('vendorscredit')->__('Withdrawal Request')));
      
      $paymentMethod 	= Mage::registry('payment_method');
      $vendor 			= Mage::getSingleton('vendors/session')->getVendor();
      $withdrawalData 	= Mage::registry('withdrawal_data');
	
      $fieldset->addField('payment_method', 'label', array(
          'label'     => Mage::helper('vendorscredit')->__('Payment Method'),
          'name'      => 'payment_method',
      ));
      $form->getElement('payment_method')->setValue($paymentMethod->getName());
  	  
      
      $additionalData = unserialize($paymentMethod->getAdditionalInfo());
      if(isset($additionalData['allow_email_account']) && $additionalData['allow_email_account']){
	      $fieldset->addField('payment_account', 'label', array(
	          'label'     => $paymentMethod->getName().' '.Mage::helper('vendorscredit')->__('Account Email'),
	          'class'     => 'validate-email',
	          'required'  => true,
	          'name'      => 'account',
	      ));
	      $form->getElement('payment_account')->setValue($withdrawalData['account']);
      }
      
      $fieldset->addField('amount', 'label', array(
          'label'     => Mage::helper('vendorscredit')->__('Amount'),
          'name'      => 'amount',
      ));
      $form->getElement('amount')->setValue(Mage::helper('core')->currency($withdrawalData['amount'],true,false));
      
      
      $fieldset->addField('fee', 'label', array(
          'label'     => Mage::helper('vendorscredit')->__('Withdrawal Fee'),
          'name'      => 'fee',
      ));
      $fee = Mage::helper('vendorscredit')->__("%s fee is taken.",Mage::helper('core')->currency($paymentMethod->getFee(),true,false));
      $form->getElement('fee')->setValue($fee);
      
      $fieldset->addField('net_amount', 'label', array(
          'label'     => Mage::helper('vendorscredit')->__('Net Amount'),
          'name'      => 'net_amount',
      ));
      $form->getElement('net_amount')->setValue(Mage::helper('core')->currency($withdrawalData['amount'] - $paymentMethod->getFee(),true,false));
      
  	  if(isset($additionalData['allow_additional_textarea']) && $additionalData['allow_additional_textarea']){
  	  	  $fieldset->addType('payment_note', 'VES_VendorsCredit_Block_Form_Element_Note');
	      $fieldset->addField('additional_info', 'note', array(
	          'label'     => $paymentMethod->getName().' '.Mage::helper('vendorscredit')->__('Info'),
	          'required'  => true,
	          'name'      => 'additional_info',
	      ));
	       $form->getElement('additional_info')->setText('<pre>'.$withdrawalData['additional_info'].'</pre>');
      }
      
      return parent::_prepareForm();
  }
}