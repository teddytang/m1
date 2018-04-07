<?php
class VES_VendorsMessage_Block_Customer_Links extends Mage_Core_Block_Template{
	/**
     * Add message link to parent block
     *
     * @return Mage_Checkout_Block_Links
     */
    public function addMessageLink()
    {
        $parentBlock = $this->getParentBlock();
        if ($parentBlock && Mage::helper('core')->isModuleOutputEnabled('VES_VendorsMessage')) {
        	$customerSession = Mage::getSingleton('customer/session');
        	if($customerSession->isLoggedIn()){
        		$customer 	= $customerSession->getCustomer();
            	$count 		= Mage::helper('vendorsmessage')->getUnreadMessageCount($customer->getId(), VES_VendorsMessage_Model_Message::TYPE_CUSTOMER);
            	if($count >0)
            		$text		= $this->__('Message (%s)',$count);
            	else
            		$text		= $this->__('Message',$count);
        	}else{
        		$text		= $this->__('Message');
        	}
        	
            $parentBlock->removeLinkByUrl($this->getUrl('customer/message/inbox'));
            $parentBlock->addLink($text, 'customer/message/inbox', $text, true, array(), 11, null, 'class="top-link-message"');
        }
        return $this;
    }
}
