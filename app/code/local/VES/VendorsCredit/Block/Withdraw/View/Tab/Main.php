<?php

class VES_VendorsCredit_Block_Withdraw_View_Tab_Main extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('withdrawal_form', array('legend'=>Mage::helper('vendorscredit')->__('Withdrawal information')));
      
      $transactionTypes = array(
      	'add_credit' 	=> Mage::helper('vendorscredit')->__('Add Credit'),
      	'deduct_credit' => Mage::helper('vendorscredit')->__('Deduct Credit'),
      );
      
      $fieldset->addField('method', 'label', array(
          'label'     => Mage::helper('vendorscredit')->__('Payment Method'),
          'name'      => 'method',
      ));
      $fieldset->addType('amount','VES_VendorsCredit_Block_Form_Element_Withdrawal_Currency');
	  $fieldset->addField('amount', 'amount', array(
          'label'     => Mage::helper('vendorscredit')->__('Amount'),
          'name'      => 'amount',
	  	  'negative'  => false,
      ));
      $fieldset->addField('fee', 'amount', array(
          'label'     => Mage::helper('vendorscredit')->__('Fee Amount'),
          'name'      => 'fee',
      	  'negative'  => true,
      ));
      $fieldset->addField('net_amount', 'amount', array(
          'label'     => Mage::helper('vendorscredit')->__('Net Amount'),
          'name'      => 'net_amount',
      	  'negative'  => false,
      ));
      $fieldset->addType('vendor_status','VES_VendorsCredit_Block_Form_Element_Withdrawal_Status');
      $fieldset->addField('status', 'vendor_status', array(
          'label'     => Mage::helper('vendorscredit')->__('Status'),
          'name'      => 'status',
      	  'options'	  => Mage::getModel('vendorscredit/source_withdrawal_status')->getOptionArray(),
      ));
      
      $fieldset->addType('custom_date','VES_VendorsCredit_Block_Form_Element_Withdrawal_Date');      
      $fieldset->addField('created_at', 'custom_date', array(
          'label'     => Mage::helper('vendorscredit')->__('Created At'),
          'name'      => 'created_at',
      ));
      $fieldset->addField('updated_at', 'custom_date', array(
          'label'     => Mage::helper('vendorscredit')->__('Updated At'),
          'name'      => 'updated_at',
      ));
      
      $fieldset->addType('additional_info','VES_VendorsCredit_Block_Form_Element_Withdrawal_Info');
      $fieldset->addField('additional_info', 'additional_info', array(
          'label'     => Mage::helper('vendorscredit')->__('Additional Info'),
          'required'  => false,
          'name'      => 'additional_info',
      ));
      $fieldset->addType('custom_note','VES_VendorsCredit_Block_Form_Element_Withdrawal_Note');
      $fieldset->addField('note', 'custom_note', array(
          'label'     => Mage::helper('vendorscredit')->__('Note'),
          'required'  => false,
          'name'      => 'note',
      ));
      if ( Mage::getSingleton('adminhtml/session')->getVendorsData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getVendorsData());
          Mage::getSingleton('adminhtml/session')->setVendorsData(null);
      } elseif ( Mage::registry('withdrawal_data') ) {
          $form->setValues(Mage::registry('withdrawal_data')->getData());
      }
      return parent::_prepareForm();
  }
}