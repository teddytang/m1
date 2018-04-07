<?php
class VES_VendorsMessage_Block_Customer_Message_Reply extends Mage_Core_Block_Template{
	
	/**
	 * Get messages
	 */
	public function getMessage(){
		return Mage::registry('message_data');
	}
	
	public function getUrlReply(){
		return $this->getUrl('customer/message/save',array('id'=>$this->getMessage()->getId()));
	}
	
	
}

