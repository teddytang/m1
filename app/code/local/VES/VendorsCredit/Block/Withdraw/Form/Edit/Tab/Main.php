<?php

class VES_VendorsCredit_Block_Withdraw_Form_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('vendors_form', array('legend'=>Mage::helper('vendorscredit')->__('Withdrawal Request')));
      
      $paymentMethod = Mage::registry('payment_method');
      $vendor = Mage::getSingleton('vendors/session')->getVendor();
      
  	  if($paymentMethod->getNote()){
      	$fieldset->addType('payment_note', 'VES_VendorsCredit_Block_Form_Element_Note');
      	$fieldset->addField('payment_note', 'payment_note', array(
	          'label'     => $paymentMethod->getName().' '.Mage::helper('vendorscredit')->__('Note'),
	          'name'      => 'payment_note',
      		  'payment_note'	=> $paymentMethod->getNote(),
	    ));
      }
      $fieldset->addField('payment_method', 'label', array(
          'label'     => Mage::helper('vendorscredit')->__('Payment Method'),
          'name'      => 'payment_method',
      ));
      $fieldset->addField('credit_balance', 'label', array(
          'label'     => Mage::helper('vendorscredit')->__('Your balance'),
          'name'      => 'credit_balance',
      ));
      $fieldset->addField('fee', 'label', array(
          'label'     => Mage::helper('vendorscredit')->__('Fee'),
          'name'      => 'fee',
      ));
      
      $additionalData = unserialize($paymentMethod->getAdditionalInfo());
      if(isset($additionalData['allow_email_account']) && $additionalData['allow_email_account']){
	      $fieldset->addField('payment_account', 'text', array(
	          'label'     => $paymentMethod->getName().' '.Mage::helper('vendorscredit')->__('Account Email'),
	          'class'     => 'validate-email',
	          'required'  => true,
	          'name'      => 'account',
	      ));
      }
      $note = '';
  	  if($paymentMethod->getMin() > 0) $note .= Mage::helper('vendorscredit')->__('Min %s, ',Mage::helper('core')->currency($paymentMethod->getMin(),true,false));
  	  if($paymentMethod->getMax() > 0) $note .= Mage::helper('vendorscredit')->__('Max %s',Mage::helper('core')->currency($paymentMethod->getMax(),true,false));
  	  $maxAmount = $paymentMethod->getMax()<$vendor->getCredit()?($paymentMethod->getMax()>0?$paymentMethod->getMax():$vendor->getCredit()):$vendor->getCredit();
      $fieldset->addField('withdraw_amount', 'text', array(
          'label'     => Mage::helper('vendorscredit')->__('Withdrawal Amount'),
          'class'     => 'validate-number validate-greater-than-zero validate-less-than less-than-'.$vendor->getCredit().($note?' validate-number-range number-range-'.$paymentMethod->getMin().'-'.($paymentMethod->getMax()>0?$paymentMethod->getMax():99999999):''),
          'required'  => true,
          'name'      => 'amount',
      	  'note'	  => $note,
      ));
  	  if(isset($additionalData['allow_additional_textarea']) && $additionalData['allow_additional_textarea']){
	      $fieldset->addField('additional_info', 'textarea', array(
	          'label'     => $paymentMethod->getName().' '.Mage::helper('vendorscredit')->__('Info'),
	          'required'  => true,
	          'name'      => 'additional_info',
	      ));
      }
      
      $form->getElement('payment_method')->setValue($paymentMethod->getName());
      $fee = Mage::helper('vendorscredit')->__("%s fee is taken.",Mage::helper('core')->currency($paymentMethod->getFee(),true,false));
      $form->getElement('fee')->setValue($fee);
      
      $form->getElement('credit_balance')->setValue(Mage::helper('core')->currency($vendor->getCredit(),true,false));
      
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