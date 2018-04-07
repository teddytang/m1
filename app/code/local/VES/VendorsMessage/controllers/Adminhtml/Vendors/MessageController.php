<?php
class VES_VendorsMessage_Adminhtml_Vendors_MessageController extends Mage_Adminhtml_Controller_Action
{
	/**
	 * Register messagebox for all controller actions
	 */
	public function preDispatch(){
		parent::preDispatch();
		if(!Mage::registry('message_box')){
			$messageBox = Mage::helper('vendorsmessage')->getMsgboxByAccount(null,VES_VendorsMessage_Model_Message::TYPE_ADMIN);
			Mage::register('message_box', $messageBox);
		}
		return $this;
	}
	
	/**
     * New messages page
     */
	public function newAction()
    {
    	$this->_forward('view');
    }
    
	/**
     * Inbox page
     */
	public function inboxAction()
    {
    	$messageCount = Mage::helper('vendorsmessage')->getUnreadMessageCount(null, VES_VendorsMessage_Model_Message::TYPE_ADMIN);
    	$this->_title(Mage::helper('vendorsmessage')->__('Inbox '.($messageCount?"($messageCount) ":'')));
    	$this->loadLayout();
    	$this->_addBreadcrumb(Mage::helper('vendorsmessage')->__('Messages'), Mage::helper('vendorsmessage')->__('Messages'))->_addBreadcrumb(Mage::helper('vendorsmessage')->__('Inbox'), Mage::helper('vendorsmessage')->__('Inbox'));
    	Mage::register('message_state', array(VES_VendorsMessage_Model_Message::STATE_UNREAD,VES_VendorsMessage_Model_Message::STATE_READ));
    	$this->renderLayout();
    }
    
	/**
     * Outbox page
     */
	public function outboxAction()
    {
    	$this->_title(Mage::helper('vendorsmessage')->__('Outbox'));
    	$this->loadLayout();
    	Mage::register('message_state', array(VES_VendorsMessage_Model_Message::STATE_SENT));
    	$this->_addBreadcrumb(Mage::helper('vendorsmessage')->__('Messages'), Mage::helper('vendorsmessage')->__('Messages'))->_addBreadcrumb(Mage::helper('vendorsmessage')->__('Outbox'), Mage::helper('vendorsmessage')->__('Outbox'));
    	$this->renderLayout();
    }
    
	/**
     * Outbox page
     */
	public function trashAction()
    {
    	$this->_title(Mage::helper('vendorsmessage')->__('Trash'));
    	$this->loadLayout();
    	Mage::register('message_state', array(VES_VendorsMessage_Model_Message::STATE_DELETED));
    	$this->_addBreadcrumb(Mage::helper('vendorsmessage')->__('Messages'), Mage::helper('vendorsmessage')->__('Messages'))->_addBreadcrumb(Mage::helper('vendorsmessage')->__('Trash'), Mage::helper('vendorsmessage')->__('Trash'));
    	$this->renderLayout();
    }
    
    public function viewAction(){
   	 	$id     = $this->getRequest()->getParam('message_id');
		$message  = Mage::getModel('vendorsmessage/message')->load($id);

		if ($message->getId() || $id == 0) {
			$data = $this->_getSession()->getFormData(true);
			if (!empty($data)) {
				$message->setData($data);
			}
			if($message->getId()){
				if(($message->getMsgboxId() != Mage::registry('message_box')->getId())){
	    			$this->_getSession()->addError(Mage::helper('vendorsmessage')->__('You do not have permission to access this page.'));
					$this->_redirect('*/*/');
					return;
	    		}
				
				/*mark message as read when you view that message*/
				if($message->getState() == VES_VendorsMessage_Model_Message::STATE_UNREAD){
	    			$message->setState(VES_VendorsMessage_Model_Message::STATE_READ)->save();
	    		}
			}
			
			Mage::register('message_data', $message);
			
			$this->loadLayout();
	    	$this->_addBreadcrumb(Mage::helper('vendorsmessage')->__('Messages'), Mage::helper('vendorsmessage')->__('Messages'),Mage::getUrl('vendors/message/inbox'));
	    	if($message->getId()){
	    		$this->_title(Mage::helper('vendorsmessage')->__('Vew Message'));
	    		$this->_addBreadcrumb(Mage::helper('vendorsmessage')->__('Vew Message'), Mage::helper('vendorsmessage')->__('Vew Message'));
	    	}else{
	    		$this->_title(Mage::helper('vendorsmessage')->__('New Message'));
	    		$this->_addBreadcrumb(Mage::helper('vendorsmessage')->__('New Message'), Mage::helper('vendorsmessage')->__('New Message'));
	    	}
	    	
			$this->renderLayout();
		} else {
			$this->_getSession()->addError(Mage::helper('vendorsmessage')->__('Message does not exist'));
			$this->_redirect('*/*/inbox');
		}
    }
    
    public function saveAction(){
    	try{
			$data = $this->getRequest()->getParam('message');
			$messageId = $this->getRequest()->getParam('id');
			
			if($messageId){
				/*Reply to a message*/
				$message = Mage::getModel('vendorsmessage/message')->load($messageId);
				if(!$message->getId() || ($message->getMsgboxId() != Mage::registry('message_box')->getId())){
					throw new Exception(Mage::helper('vendorsmessage')->__('You do not have permission to access this page.'));
				}
				$message->sendReply($data['content']);
			}else{
				/*Send new message*/
				$message = Mage::getModel('vendorsmessage/message');
				$toMsgBox = Mage::helper('vendorsmessage')->getMsgboxByEmail($data['to'],$data['recipient_type']);
				$message->sendNewMessage($data,Mage::registry('message_box'),$data['recipient_type'],$toMsgBox);
			}
	    	$this->_getSession()->addSuccess(Mage::helper('vendorsmessage')->__('Your message has been sent.'));
		}catch (Exception $e){
			$this->_getSession()->addError($e->getMessage());
		}
    	$this->_redirect('*/*/inbox');
    }
    
    
    
    public function markunreadAction(){
    	$messageId = $this->getRequest()->getParam('message_id');
    	try {
    		$message = Mage::getModel('vendorsmessage/message')->load($messageId);
        	$message->setState(VES_VendorsMessage_Model_Message::STATE_UNREAD)->save();
        	$this->_getSession()->addSuccess(Mage::helper('vendorsmessage')->__('Your message has been marked as unread.'));
    	}catch (Exception $e) {
			$this->_getSession()->addError($e->getMessage());
		}
		$this->_redirect('*/*/inbox');
    }
    public function massMarkAsUnreadAction(){
    	$messageIds = explode(",",$this->getRequest()->getParam('messages'));
        if(!is_array($messageIds)) {
			$this->_getSession()->addError(Mage::helper('vendorsmessage')->__('Please select item(s)'));
        } else {
            try {
                foreach ($messageIds as $messageId) {
                    $message = Mage::getModel('vendorsmessage/message')->load($messageId);
                    $message->setState(VES_VendorsMessage_Model_Message::STATE_UNREAD)->save();
                }
               	$this->_getSession()->addSuccess(Mage::helper('vendorsmessage')->__('Total of %d message(s) were successfully processed', count($messageIds)));
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/inbox');
    }
    
	public function massMarkAsReadAction(){
    	$messageIds = explode(",",$this->getRequest()->getParam('messages'));
        if(!is_array($messageIds)) {
			$this->_getSession()->addError(Mage::helper('vendorsmessage')->__('Please select item(s)'));
        } else {
            try {
                foreach ($messageIds as $messageId) {
                    $message = Mage::getModel('vendorsmessage/message')->load($messageId);
                    $message->setState(VES_VendorsMessage_Model_Message::STATE_READ)->save();
                }
               	$this->_getSession()->addSuccess(Mage::helper('vendorsmessage')->__('Total of %d message(s) were successfully processed', count($messageIds)));
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/inbox');
    }
    
	public function deleteAction(){
    	$messageId = $this->getRequest()->getParam('message_id');
		try {
			$vendor = $this->_getSession()->getVendor();
			$message = Mage::getModel('vendorsmessage/message')->load($messageId);
			
			if($message->getMsgboxId() != Mage::registry('message_box')->getId()){
    			$this->_getSession()->addError(Mage::helper('vendorsmessage')->__('You do not have permission to access this page.'));
				$this->_redirect('*/*/');
				return;
    		}
    		
			if($message->getData('state') == VES_VendorsMessage_Model_Message::STATE_DELETED){
				$message->delete();
			}
			else{
				$message->setState(VES_VendorsMessage_Model_Message::STATE_DELETED)->setData('owner_id',$vendor->getId())->save();
			}
			$this->_getSession()->addSuccess(Mage::helper('vendorsmessage')->__('Your message were successfully deleted'));
		} catch (Exception $e) {
			$this->_getSession()->addError($e->getMessage());
		}
        $this->_redirect('*/*/inbox');
    }
    
	public function massDeleteAction(){
    	$messageIds = explode(",",$this->getRequest()->getParam('messages'));
    	$vendor = $this->_getSession()->getVendor();
        if(!is_array($messageIds)) {
			$this->_getSession()->addError(Mage::helper('vendorsmessage')->__('Please select item(s)'));
        } else {
            try {
                foreach ($messageIds as $messageId) {
                    $message = Mage::getModel('vendorsmessage/message')->load($messageId);
                	if($message->getMsgboxId() != Mage::registry('message_box')->getId()){
		    			$this->_getSession()->addError(Mage::helper('vendorsmessage')->__('You do not have permission to access this page.'));
						$this->_redirect('*/*/');
						return;
		    		}
                    if($message->getData('state') == VES_VendorsMessage_Model_Message::STATE_DELETED){
                    	$message->delete();
                    }
                    else{
                    	$message->setState(VES_VendorsMessage_Model_Message::STATE_DELETED)->setData('owner_id',$vendor->getId())->save();
                    }
                }
               	$this->_getSession()->addSuccess(Mage::helper('vendorsmessage')->__('Total of %d message(s) were successfully deleted', count($messageIds)));
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirectReferer();
    }
    
    
    public function massRestoreAction(){
    	$messageIds = explode(",",$this->getRequest()->getParam('messages'));
    	$vendor = $this->_getSession()->getVendor();
    	if(!is_array($messageIds)) {
			$this->_getSession()->addError(Mage::helper('vendorsmessage')->__('Please select item(s)'));
        } else {
            try {
                foreach ($messageIds as $messageId) {
                    $message = Mage::getModel('vendorsmessage/message')->load($messageId);
                	
                    if($message->getMsgboxId() != Mage::registry('message_box')->getId()){
		    			$this->_getSession()->addError(Mage::helper('vendorsmessage')->__('You do not have permission to access this page.'));
						$this->_redirect('*/*/');
						return;
		    		}
		    		if($message->getFromMsgboxId() != Mage::registry('message_box')->getId()){
		    			/*Inbox Message*/
		    			$message->setState(VES_VendorsMessage_Model_Message::STATE_READ)->save();
		    		}else{
		    			/*Outbox Message*/
		    			$message->setState(VES_VendorsMessage_Model_Message::STATE_SENT)->save();
		    		}
                }
               	$this->_getSession()->addSuccess(Mage::helper('vendorsmessage')->__('Total of %d message(s) were successfully restored', count($messageIds)));
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirectReferer();
    }
}