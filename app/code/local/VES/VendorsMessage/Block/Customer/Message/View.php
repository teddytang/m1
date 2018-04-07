<?php
class VES_VendorsMessage_Block_Customer_Message_View extends Mage_Core_Block_Template{
	
	/**
	 * Get messages
	 */
	public function getMessage(){
		return Mage::registry('message_data');
	}
	
	public function getBackUrl(){
		return $this->getUrl('customer/message');
	}
}
