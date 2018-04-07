<?php

class VES_VendorsMessage_Model_Message extends Mage_Core_Model_Abstract
{
	
	const STATE_DELETED 	= 0;
	const STATE_UNREAD 		= 1;
	const STATE_READ 		= 2;
	const STATE_SENT 		= 3;
	
	const TYPE_ADMIN		= 'admin';
	const TYPE_VENDOR		= 'vendor';
	const TYPE_CUSTOMER		= 'customer';
	
	const MS_NEW 	= "new";
	const MS_REPLY 	= "reply";
			
	const XML_PATH_MESSAGE_NOTIFICATION = 'vendors/message/notification_email_template';
	const XML_PATH_MESSAGE_SENDER 		= 'vendors/message/email_identity';
	
	protected $_parent_message;
	protected $_msgbox;
	protected $_from_msgbox;
	protected $_to_msgbox;
	
	
	/**
	 * Get account info by Message Box Id
	 * @param string $msgboxId
	 */
	protected function _getAccountInfo($msgboxId){
		$msgbox = Mage::getModel('vendorsmessage/msgbox')->load($msgboxId);
		switch ($msgbox->getType()){
			case self::TYPE_VENDOR:
				$vendor		= Mage::getModel('vendors/vendor')->load($msgbox->getOwnerId());
				if(!$vendor->getId()) throw new Mage_Core_Exception(Mage::helper('vendorsmessage')->__('Vendor account does not exist'));
				$accountObj	= $vendor;
				break;
			case self::TYPE_CUSTOMER:
				$customer		= Mage::getModel('customer/customer')->load($msgbox->getOwnerId());
				if(!$customer->getId()) throw new Mage_Core_Exception(Mage::helper('vendorsmessage')->__('Customer account does not exist'));
				$accountObj	= $customer;
				break;
			case self::TYPE_ADMIN:
				//$admin			= Mage::getModel('admin/user')->load($msgbox->getOwnerId());
				//if(!$admin->getId()) throw new Mage_Core_Exception(Mage::helper('vendorsmessage')->__('Admin account does not exist'));
				$accountObj	= $msgbox;
				break;
		}
		return $accountObj;
	}
	
	public function _construct()
    {
        parent::_construct();
        $this->_init('vendorsmessage/message');
    }
	
	/**
     * Send new message for current message
     * @param string $messageContent
     * @param VES_VendorsMessage_Model_Msgbox $fromMsgbox
     * @param string $toTypeAccount
     * @param VES_VendorsMessage_Model_Msgbox $toMsgbox
     */
    public function sendNewMessage($data,$fromMsgbox,$toTypeAccount, $toMsgbox){
    	$toEmail = $toMsgbox->getEmail();
    	if(!$toEmail) throw new Exception(Mage::helper('vendorsmessage')->__('Recipient email is not exist.'));
    	$data = array(
    		'msgbox_id'			=> $toMsgbox->getId(),
			'parent_message_id'	=> '',
			'from_msgbox_id'	=> $fromMsgbox->getId(),
    		'from'				=> $fromMsgbox->getEmail(),
			'to_msgbox_id'	=> $toMsgbox->getId(),
			'to'			=> $toEmail,
			'subject'		=> $data['subject'],
			'content'		=> $data['content'],
			'state'			=> self::STATE_UNREAD,
			'created_at'	=> now(),
			'updated_at'	=> now(),
    	);
    	$this->setData($data)->save();
    	/*Send notification email to recipient */
    	$this->sendNotificationEmail();
    	
    	/*Save a copy of message to sender account as sent email*/
    	$data['msgbox_id']	= $fromMsgbox->getId();
    	$data['state']		= self::STATE_SENT;
    	Mage::getModel('vendorsmessage/message')->setData($data)->save();
    }
    
    /**
     * Send reply message for current message
     * @param string $messageContent
     */
	public function sendReply($messageContent){
		$message = Mage::getModel('vendorsmessage/message');
		if($this->getData('state') == self::STATE_SENT){
            $data = array(
                'msgbox_id'			=> $this->getData('to_msgbox_id'),
                'parent_message_id'	=> $this->getData('parent_message_id')?$this->getData('parent_message_id'):$this->getData('message_id'),
                'from_msgbox_id'	=> $this->getData('from_msgbox_id'),
                'from'				=> $this->getFrom(),
                'to_msgbox_id'	=> $this->getData('to_msgbox_id'),
                'to'			=> $this->getTo(),
                'subject'		=> 'Re:'.str_replace("Re:","",$this->getData('subject')),
                'content'		=> $messageContent,
                'state'			=> self::STATE_UNREAD,
                'created_at'	=> now(),
                'updated_at'	=> now(),
            );
            /*Save message to recipient account*/
            $message->setData($data)->save();
            /*Send notification email to recipient*/
            $message->sendNotificationEmail();


            /*Save message as sent message for sender*/
            $data['msgbox_id'] 	= $this->getData('from_msgbox_id');
            $data['state']		= self::STATE_SENT;

            Mage::getModel('vendorsmessage/message')->setData($data)->save();
        }
        else{
            $data = array(
                'msgbox_id'			=> $this->getData('from_msgbox_id'),
                'parent_message_id'	=> $this->getData('parent_message_id')?$this->getData('parent_message_id'):$this->getData('message_id'),
                'from_msgbox_id'	=> $this->getData('to_msgbox_id'),
                'from'				=> $this->getTo(),
                'to_msgbox_id'	=> $this->getData('from_msgbox_id'),
                'to'			=> $this->getFrom(),
                'subject'		=> 'Re:'.str_replace("Re:","",$this->getData('subject')),
                'content'		=> $messageContent,
                'state'			=> self::STATE_UNREAD,
                'created_at'	=> now(),
                'updated_at'	=> now(),
            );
            /*Save message to recipient account*/
            $message->setData($data)->save();
            /*Send notification email to recipient*/
            $message->sendNotificationEmail();


            /*Save message as sent message for sender*/
            $data['msgbox_id'] 	= $this->getData('to_msgbox_id');
            $data['state']		= self::STATE_SENT;

            Mage::getModel('vendorsmessage/message')->setData($data)->save();
        }
	}
	
	
	public function getViewMessageUrl($msgboxId){

		$msgbox = Mage::getModel('vendorsmessage/msgbox')->load($msgboxId);
	
		switch ($msgbox->getType()){
			case self::TYPE_VENDOR:
				return Mage::getUrl('vendors/message/view/',array('message_id'=>$this->getId()));
				break;
			case self::TYPE_CUSTOMER:
				return Mage::getUrl('customer/message/view/',array('message_id'=>$this->getId()));
				break;
			case self::TYPE_ADMIN:
				return Mage::getUrl('adminhtml/message/view/',array('message_id'=>$this->getId()));
				break;
		}
				
	}
	
	public function sendNotificationEmail(){
		$sender 	= $this->_getAccountInfo($this->getData('from_msgbox_id'));
		$recipient	= $this->_getAccountInfo($this->getData('to_msgbox_id'));
		
		$mailer = Mage::getModel('core/email_template_mailer');
        $emailInfo = Mage::getModel('core/email_info');
        $emailInfo->addTo($recipient->getEmail(), $recipient->getName());
        $mailer->addEmailInfo($emailInfo);
        // Set all required params and send emails
        $mailer->setSender(Mage::getStoreConfig(self::XML_PATH_MESSAGE_SENDER));
        $mailer->setTemplateId(Mage::getStoreConfig(self::XML_PATH_MESSAGE_NOTIFICATION));
        $mailer->setTemplateParams(array('sender'=>$sender, 'recipient'=>$recipient,'message'=>$this,'message_url'=>$this->getViewMessageUrl($this->getData('to_msgbox_id'))));
        $mailer->send();
	}
	/**
	 * Get the owner message box
	 */
	public function getMsgbox(){
		if(!$this->_msgbox){
			$this->_msgbox = Mage::getModel('vendorsmessage/msgbox')->load($this->getMsgboxId());
		}
		return $this->_msgbox;
	}
	
	public function getFromMsgbox(){
		if(!$this->_from_msgbox){
			$this->_from_msgbox = Mage::getModel('vendorsmessage/msgbox')->load($this->getFromMsgboxId());
		}
		return $this->_from_msgbox;
	}
	
	public function getToMsgbox(){
		if(!$this->_to_msgbox){
			$this->_to_msgbox = Mage::getModel('vendorsmessage/msgbox')->load($this->getToMsgboxId());
		}
		return $this->_to_msgbox;
	}
	
	public function getParentMessage(){
		if(!$this->_parent_message){
			$this->_parent_message = Mage::getModel('vendorsmessage/message')->load($this->getParentMessageId());
		}
		return $this->_parent_message;
		
	}
}