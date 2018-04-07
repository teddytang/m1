<?php

class VES_VendorsMessage_Block_Message_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareLayout(){
		$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
		parent::_prepareLayout();
	}
	
	protected function _prepareForm(){
		$form = new Varien_Data_Form(array(
			'id' => 'edit_form',
			'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('message_id'))),
			'method' => 'post',
			'enctype' => 'multipart/form-data'
			)
		);
	
		$form->setUseContainer(true);
		$this->setForm($form);
		$fieldset 	= $form->addFieldset('vendors_form', array('legend'=>Mage::helper('vendorsmessage')->__('Message information'),'class'=>'fieldset-wide'));
		$message 	= Mage::registry('message_data');
		if($message->getId()){
			$fieldset->addType('message_content','VES_VendorsMessage_Block_Form_Element_Note');
			$fieldset->addField('content', 'message_content', array(
				'label'     => Mage::helper('vendorsmessage')->__('Content'),
				'name'      => 'content',
				'message'	=> $message,
			));
		}else{
			$values = array(''=> Mage::helper('vendorsmessage')->__('-= Select recipient type =-'));
			if(Mage::app()->getStore()->isAdmin()){
				$values[VES_VendorsMessage_Model_Message::TYPE_VENDOR] = Mage::helper('vendorsmessage')->__('Vendor');
			}else{
				$values[VES_VendorsMessage_Model_Message::TYPE_ADMIN] = Mage::helper('vendorsmessage')->__('Admin');
			}
			$values[VES_VendorsMessage_Model_Message::TYPE_CUSTOMER] = Mage::helper('vendorsmessage')->__('Customer');
			
			$fieldset->addField('recipient_type', 'select', array(
				'label'     => Mage::helper('vendorsmessage')->__('To'),
				'class'     => 'required-entry',
				'required'  => true,
				'name'      => 'message[recipient_type]',
				'values'	=> $values,
			));
			
			$fieldset->addField('to', 'text', array(
				'label'     => Mage::helper('vendorsmessage')->__('Recipient'),
				'class'     => 'required-entry validate-email',
				'required'  => true,
				'name'      => 'message[to]',
			));
			
			$fieldset->addField('subject', 'text', array(
				'label'     => Mage::helper('vendorsmessage')->__('Subject'),
				'class'     => 'required-entry',
				'required'  => true,
				'name'      => 'message[subject]',
			));
		}
		$config = Mage::getSingleton('cms/wysiwyg_config')->getConfig();
		$config['add_variables']	= false;
		$config['add_widgets']	= false;
		$fieldset->addType('message_editor','VES_VendorsMessage_Block_Form_Element_Editor');
		$fieldset->addField('content_editor', 'message_editor', array(
			'label'     => Mage::helper('vendorsmessage')->__('Content'),
			'name'      => 'message[content]',
			'config'	=> $config,
			'wysiwyg'	=> Mage::helper('vendorsmessage')->isEnableEditor(),
			'required'  => true,
		));

		if ( Mage::getSingleton('adminhtml/session')->getVendorsData() ){
			$form->setValues(Mage::getSingleton('adminhtml/session')->getVendorsData());
			Mage::getSingleton('adminhtml/session')->setVendorsData(null);
		} elseif ( Mage::registry('message_data') ) {
			$form->setValues(Mage::registry('message_data')->getData());
		}
		return parent::_prepareForm();
	}
}