<?php
class VES_VendorsMessage_Block_Notification extends Mage_Core_Block_Template
{
    protected function _toHtml(){
    	if(!Mage::helper('vendorsmessage')->moduleEnable()) return '';
    	return parent::_toHtml();
    }
	public function getUnreadMessageCount(){
      	return  $this->helper('vendorsmessage')->getUnreadMessageCount(Mage::getSingleton('vendors/session')->getVendorId(), VES_VendorsMessage_Model_Message::TYPE_VENDOR);
    }
    public function canShow()
    {
    	if(!$this->getUnreadMessageCount()) return false;
    	if(!Mage::getSingleton('vendors/session')->getData('notification_is_displayed')){
    		Mage::getSingleton('vendors/session')->setData('notification_is_displayed',true);
    		return true;
    	}
    	return false;
    }
}