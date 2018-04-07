<?php
class VES_VendorsMessage_Block_Vendor_Profile extends Mage_Core_Block_Template{
	protected function _toHtml(){
		if(!Mage::helper('vendorsmessage')->moduleEnable()) return '';
		return parent::_toHtml();
	}
	public function getVendorId(){
		return Mage::registry('vendor_id');
	}
	
	public function getMessageBoxId(){
		$messageBox = Mage::helper('vendorsmessage')->getMsgboxByAccount(Mage::registry('vendor')->getId(),VES_VendorsMessage_Model_Message::TYPE_VENDOR);
		return $messageBox->getId();
	}
}
