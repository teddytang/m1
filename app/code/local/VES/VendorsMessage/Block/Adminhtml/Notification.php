<?php
class VES_VendorsMessage_Block_Adminhtml_Notification extends Mage_Adminhtml_Block_Template
{
	protected $_unread_message;
	public function getUnreadMessages(){
		if(!$this->_unread_messages){
			$messageBox = Mage::getModel('vendorsmessage/msgbox')->getCollection()->addFieldToFilter('type',VES_VendorsMessage_Model_Message::TYPE_ADMIN);
			if(!$messageBox->count()) {
				$messageBox = Mage::helper('vendorsmessage')->getMsgboxByEmail('',VES_VendorsMessage_Model_Message::TYPE_ADMIN);
			}else $messageBox = $messageBox->getFirstItem();
			$this->_unread_message = Mage::getModel('vendorsmessage/message')->getCollection()->addFieldToFilter('msgbox_id',$messageBox->getId())->addFieldToFilter('state',VES_VendorsMessage_Model_Message::STATE_UNREAD)->addOrder('created_at','desc');
		}
		return $this->_unread_message;
	}
	
	public function getUnreadMessageCount(){
		return $this->getUnreadMessages()->count();
	}
	
	public function getLastUnreadMessage(){
		return $this->getUnreadMessages()->getFirstItem();
	}
	
	protected function _toHtml(){
		if(!$this->getUnreadMessageCount()) return '';
		return parent::_toHtml();
	}
	
	
	public function getVendorMessageUrl(){
		return $this->getUrl('adminhtml/vendors_message/inbox');
	}	
	
}