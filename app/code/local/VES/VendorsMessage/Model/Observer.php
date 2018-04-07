<?php
/**
 *
 * @author		VnEcoms Team <support@vnecoms.com>
 * @website		http://www.vnecoms.com
 */
class VES_VendorsMessage_Model_Observer
{
	public function ves_vendorspage_profile_prepare(Varien_Event_Observer $observer){
		$profileBlock = $observer->getProfileBlock();
		$messageBlock = $profileBlock->getLayout()->createBlock('vendorsmessage/vendor_profile','vendor.message')->setTemplate('ves_vendorsmessage/vendor/profile.phtml');
		$footerProfile = $profileBlock->getChild('footer_profile');
		$footerProfile->insert($messageBlock, '', false, 'vendors_message_block');
	}

}