<?php

class VES_VendorsMessage_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
	 * Is Module Enable
	 */
	public function moduleEnable(){
		$result = new Varien_Object(array('module_enable'=>true));
		Mage::dispatchEvent('ves_vendorsmessage_module_enable',array('result'=>$result));
		return $result->getData('module_enable');
	}
	/**
	 * Init message Box
	 * @param string $account
	 * @param string $type
	 */
	protected function _initMessageBox($account, $type){
		$data = array(
			'owner_id'	=> $account,
			'type'		=> $type,
		);
		switch ($type){
			case VES_VendorsMessage_Model_Message::TYPE_ADMIN:
				$data['name']	= Mage::getStoreConfig('trans_email/ident_general/name');
				$data['email']	= Mage::getStoreConfig('trans_email/ident_general/email');
				break;
			case VES_VendorsMessage_Model_Message::TYPE_VENDOR:
				$vendor = Mage::getModel('vendors/vendor')->load($account);
				$data['name']	= $vendor->getName();
				$data['email']	= $vendor->getEmail();
				break;
			case VES_VendorsMessage_Model_Message::TYPE_CUSTOMER:
				$customer = Mage::getModel('customer/customer')->load($account);
				$data['name']	= $customer->getName();
				$data['email']	= $customer->getEmail();
				break;
			default: throw new Exception($this->__('Account type %s does not exist.',$type));
		}
		$messageBox = Mage::getModel('vendorsmessage/msgbox')->setData($data)->save();
		return $messageBox;
	}
	/**
	 * Is enable WYSIWYG editor for message editor 
	 */
	public function isEnableEditor(){
		return Mage::getStoreConfig('vendors/message/enable_editor');
	}
	/**
	 * Get New Message Url
	 */
	public function getNewMessageUrl(){
		return $this->_getUrl('vendors/message/new');
	}
	
	/**
	 * Get Inbox Url
	 */
	public function getInboxUrl(){
		return $this->_getUrl('vendors/message/inbox');
	}
	
	/**
	 * Get Outbox Url
	 */
	public function getOutboxUrl(){
		return $this->_getUrl('vendors/message/outbox');
	}
	/**
	 * Get Trash Url
	 */
	public function getTrashUrl(){
		return $this->_getUrl('vendors/message/trash');
	}
	
	/**
	 * Get message box by Email
	 * @param string $email
	 * @param string $type
	 */
	public function getMsgboxByEmail($email,$type){
		$msgbox = Mage::getResourceModel('vendorsmessage/msgbox_collection')
		->addFieldToFilter('type',$type);
		
		if($type != VES_VendorsMessage_Model_Message::TYPE_ADMIN)
		$msgbox->addFieldToFilter('email',$email);
		
		if(!$msgbox->count()){
			switch ($type){
				case VES_VendorsMessage_Model_Message::TYPE_VENDOR:
					$vendor		= Mage::getModel('vendors/vendor')->loadByEmail($email);
					$account 	= $vendor->getId();
					break;
				case VES_VendorsMessage_Model_Message::TYPE_CUSTOMER:
					$websiteId	= Mage::app()->getWebsite()->getId();
					$customer	= Mage::getModel('customer/customer')->setWebsiteId($websiteId)->loadByEmail($email);
					if(!$customer->getId()) throw new Exception($this->__('Customer email account is not exist.'));
					$account	= $customer->getId();
					break;
				case VES_VendorsMessage_Model_Message::TYPE_ADMIN:
					$account	= null;
					break;
			}
			if($type != VES_VendorsMessage_Model_Message::TYPE_ADMIN && !$account) throw new Mage_Core_Exception(Mage::helper('vendorsmessage')->__('The recipient account is not exist.'));
			$msgbox = $this->_initMessageBox($account,$type);
		}else{
			$msgbox = $msgbox->getFirstItem();
		}
		return $msgbox;
	}
	
	/**
	 * Get message box
	 * @param int $account
	 * @param string $type
	 */
	public function getMsgboxByAccount($account,$type){
		$msgbox = Mage::getResourceModel('vendorsmessage/msgbox_collection')
		->addFieldToFilter('owner_id',$account)
		->addFieldToFilter('type',$type);
		;
		if(!$msgbox->count()){
			$msgbox = $this->_initMessageBox($account,$type);
		}else{
			$msgbox = $msgbox->getFirstItem();
		}
		return $msgbox;
	}
	
	/**
	 * Get number of unread Message
	 * @param int $account
	 * @param string $type
	 */
	public function getUnreadMessageCount($account,$type){
		$msgbox = $this->getMsgboxByAccount($account, $type);
      	$collection = Mage::getModel('vendorsmessage/message')->getCollection()
      											->addFieldToFilter('msgbox_id',$msgbox->getId())
      											->addFieldToFilter('state',VES_VendorsMessage_Model_Message::STATE_UNREAD);
      	return  $collection->count();
    }
}