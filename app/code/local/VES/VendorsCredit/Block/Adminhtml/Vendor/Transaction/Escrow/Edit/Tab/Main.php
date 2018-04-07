<?php

class VES_VendorsCredit_Block_Adminhtml_Vendor_Transaction_Escrow_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('vendors_form', array('legend'=>Mage::helper('vendorscredit')->__('Transaction information')));
      
      $fieldset->addField('escrow_id', 'label', array(
          'label'     => Mage::helper('vendorscredit')->__('ID'),
          'required'  => true,
          'name'      => 'escrow_id',
      ));
      $vendor = Mage::getModel('vendors/vendor')->load(Mage::registry('current_escrow')->getVendorId());
      
      $fieldset->addField('vendor_id', 'link', array(
          'label'     => Mage::helper('vendorscredit')->__('Vendor'),
          'class'     => 'required-entry',
      	  'href'	  => $this->getUrl('*/vendors/edit',array('id'=>$vendor->getId())),
          'target'    => '_blank',
          'required'  => true,
      ));
      
      $fieldset->addField('order_id', 'link', array(
          'label'     => Mage::helper('vendorscredit')->__('Order Id'),
          'target'    => '_blank',
      ));
      $fieldset->addField('invoice_id', 'link', array(
          'label'     => Mage::helper('vendorscredit')->__('Invoice Id'),
          'target'    => '_blank',
      ));
      
      if(Mage::helper('vendors')->isAdvancedMode()){
          
      }else{
          $fieldset->addField('item_name', 'label', array(
              'label'     => Mage::helper('vendorscredit')->__('Product Name'),
          ));
          $fieldset->addField('item_sku', 'label', array(
              'label'     => Mage::helper('vendorscredit')->__('Product SKU'),
          ));
      }
      
	  $fieldset->addField('amount', 'label', array(
          'label'     => Mage::helper('vendorscredit')->__('Amount'),
          'name'      => 'amount',
      ));
      
	  $fieldset->addField('created_at', 'label', array(
	      'label'     => Mage::helper('vendorscredit')->__('Created At'),
	      'name'      => 'created_at',
	  ));
	  $fieldset->addField('status', 'label', array(
	      'label'     => Mage::helper('vendorscredit')->__('Status'),
	      'name'      => 'status',
	  ));
      if ( Mage::getSingleton('adminhtml/session')->getVendorsData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getVendorsData());
          Mage::getSingleton('adminhtml/session')->setVendorsData(null);
      } elseif ( $escrow = Mage::registry('current_escrow') ) {
          $form->setValues($escrow->getData());
          $form->getElement('vendor_id')->setValue($vendor->getVendorId());
          
          $statusOptionsArray = Mage::getModel('vendorscredit/escrow')->getStatusOptionsArray();
          $form->getElement('status')->setValue($statusOptionsArray[$escrow->getStatus()]);
          
          $information = $escrow->getInformation();
          $order = Mage::getModel('sales/order')->load($information['order']);
          $invoice = Mage::getModel('sales/order_invoice')->load($information['invoice']);
          
          $form->getElement('order_id')->setValue($order->getIncrementId())->setData('href',$this->getUrl('*/sales_order/view',array('order_id'=>$order->getId())));
          $form->getElement('invoice_id')->setValue($invoice->getIncrementId())->setData('href',$this->getUrl('*/sales_invoice/view',array('invoice_id'=>$invoice->getId())));
          
          if(Mage::helper('vendors')->isAdvancedMode()){
            
          }else{
              $invoiceItem = Mage::getModel('sales/order_invoice_item')->load($information['item']);
              
              $form->getElement('item_name')->setValue($invoiceItem->getName());
              $form->getElement('item_sku')->setValue($invoiceItem->getSku());
          }
      }
      return parent::_prepareForm();
  }
}