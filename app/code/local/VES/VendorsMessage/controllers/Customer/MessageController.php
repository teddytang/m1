<?php
class VES_VendorsMessage_Customer_MessageController extends Mage_Core_Controller_Front_Action
{
	/**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch()
    {
        // a brute-force protection here would be nice

        parent::preDispatch();

        if (!$this->getRequest()->isDispatched()) {
            return;
        }

    	if (!$this->_getSession()->authenticate($this)) {
        	$this->setFlag('', 'no-dispatch', true);
        }
    	if(!Mage::registry('message_box')){
			$messageBox = Mage::helper('vendorsmessage')->getMsgboxByAccount($this->_getSession()->getCustomerId(),VES_VendorsMessage_Model_Message::TYPE_CUSTOMER);
			Mage::register('message_box', $messageBox);
		}
    }
    
    protected function _getSession(){
    	return Mage::getSingleton('customer/session');
    }
    
    public function indexAction(){
    	$this->_forward('inbox');
    }
    
    protected function _activeNavigationMenu(){
    	$navigationBlock = $this->getLayout()->getBlock('customer_account_navigation');
        if ($navigationBlock) {
            $navigationBlock->setActive('customer/message');
        }
    }
	/**
     * Inbox page
     */
	public function inboxAction()
    {
    	$messageCount = Mage::helper('vendorsmessage')->getUnreadMessageCount($this->_getSession()->getCustomerId(), VES_VendorsMessage_Model_Message::TYPE_CUSTOMER);;
    	$this->_title(Mage::helper('vendorsmessage')->__('Inbox '.($messageCount?"($messageCount) ":'')));
    	$this->loadLayout()->_initLayoutMessages('customer/session');
    	$this->_activeNavigationMenu();
    	Mage::register('message_state', array(VES_VendorsMessage_Model_Message::STATE_UNREAD,VES_VendorsMessage_Model_Message::STATE_READ));
    	$this->renderLayout();
    }
    
	/**
     * Outbox page
     */
	public function outboxAction()
    {
    	$this->_title(Mage::helper('vendorsmessage')->__('Outbox'));
    	$this->loadLayout()->_initLayoutMessages('customer/session');
    	$this->_activeNavigationMenu();
    	Mage::register('message_state', array(VES_VendorsMessage_Model_Message::STATE_SENT));
    	$this->renderLayout();
    }
	/**
     * Outbox page
     */
	public function trashAction()
    {
    	$this->_title(Mage::helper('vendorsmessage')->__('Trash'));
    	$this->loadLayout()->_initLayoutMessages('customer/session');
    	$this->_activeNavigationMenu();
    	Mage::register('message_state', array(VES_VendorsMessage_Model_Message::STATE_DELETED));
    	$this->renderLayout();
    }
    
	/**
     * New messages page
     */
	public function newAction()
    {
    	$this->_forward('view');
    }
    
    public function viewAction(){
    	$id     = $this->getRequest()->getParam('message_id');
		$message  = Mage::getModel('vendorsmessage/message')->load($id);

		if ($message->getId() || $id == 0) {
			$data = $this->_getSession()->getFormData(true);
			if (!empty($data)) {
				$message->setData($data);
			}

			Mage::register('message_data', $message);
			
			/*mark message as read when you view that message*/
			if($message->getId()){
				if($message->getMsgboxId() != Mage::registry('message_box')->getId()){
	    			$this->_getSession()->addError(Mage::helper('vendorsmessage')->__('You do not have permission to access this page.'));
					$this->_redirect('*/*/');
					return;
	    		}
				if($message->getState() == VES_VendorsMessage_Model_Message::STATE_UNREAD){
	    			$message->setState(VES_VendorsMessage_Model_Message::STATE_READ)->save();
	    		}
			}
			$this->loadLayout()->_initLayoutMessages('customer/session');
			$this->_activeNavigationMenu();
	    	if($message->getId()){
	    		$this->_title(Mage::helper('vendorsmessage')->__('Vew Message'));
	    	}else{
	    		$this->_title(Mage::helper('vendorsmessage')->__('New Message'));
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
				$fromMsgBox = Mage::registry('message_box');
				$toMsgBox = Mage::getModel('vendorsmessage/msgbox')->load($data['message_box_id']);
				$message->sendNewMessage($data,$fromMsgBox,VES_VendorsMessage_Model_Message::TYPE_VENDOR,$toMsgBox);
				return;
			}
	    	Mage::getSingleton('core/session')->addSuccess(Mage::helper('vendorsmessage')->__('Your message has been sent.'));
		}catch (Exception $e){
			Mage::getSingleton('core/session')->addError($e->getMessage());
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
				$message->setState(VES_VendorsMessage_Model_Message::STATE_DELETED)->setData('owner_id',$this->_getSession()->getCustomer()->getId())->save();
			}
			Mage::getSingleton('core/session')->addSuccess(Mage::helper('vendorsmessage')->__('Your message were successfully deleted'));
		} catch (Exception $e) {
			Mage::getSingleton('core/session')->addError($e->getMessage());
		}
        $this->_redirect('*/*/inbox');
    }
    
	public function massDeleteAction(){
    	$messageIds = explode(",",$this->getRequest()->getParam('messages'));
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
                    $message->setState(VES_VendorsMessage_Model_Message::STATE_DELETED)->setData('owner_id',$this->_getSession()->getCustomer()->getId())->save();
                    }
                }
               Mage::getSingleton('core/session')->addSuccess(Mage::helper('vendorsmessage')->__('Total of %d message(s) were successfully deleted', count($messageIds)));
            } catch (Exception $e) {
               Mage::getSingleton('core/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/inbox');
    }
}