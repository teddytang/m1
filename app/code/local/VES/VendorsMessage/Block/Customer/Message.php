<?php
class VES_VendorsMessage_Block_Customer_Message extends Mage_Core_Block_Template{
	public function getPagerHtml()
	{
		return $this->getChildHtml('message');
	}
	/**
	 * Get Title
	 */
	public function getTitle(){
		$action = $this->getRequest()->getActionName();
		switch($action){
			case 'inbox':
				return $this->__('Inbox');
			case 'outbox':
				return $this->__('Outbox');
			case 'trash':
				return $this->__('Trash');
		}
	}
	
	/**
	 * 
	 * get Action Name
	 * @param unknown_type $dateTime
	 */
	public function getActionName(){
		return $action = $this->getRequest()->getActionName();
	}
	public function formatDateTime($dateTime){
		return Mage::getModel('core/date')->date('M d, Y h:s:i A',$dateTime);
	}
	/**
	 * Get messages
	 */
	public function getMessages(){
		$customerEmail = Mage::getSingleton('customer/session')->getCustomer()->getEmail();
		$collection = Mage::getModel('vendorsmessage/message')->getCollection();
		if($state = Mage::registry('message_state')){
	      	$msgbox = Mage::registry('message_box');
	      	$collection->addFieldToFilter('state',array('in'=>$state))->addFieldToFilter('msgbox_id',$msgbox->getId());
	    }
		$collection->addOrder('created_at','DESC');
		$message_page = $this->getLayout()->createBlock('page/html_pager', 'product.pager')
		->setCollection($collection);
		$this->setChild('message', $message_page);
		return $collection;
	}
	
	public function getViewUrl(VES_VendorsMessage_Model_Message $message){
		return $this->getUrl('customer/message/view',array('message_id'=>$message->getId()));
	}
	public function getDeleteUrl(VES_VendorsMessage_Model_Message $message){
		return $this->getUrl('customer/message/delete',array('message_id'=>$message->getId()));
	}
}
