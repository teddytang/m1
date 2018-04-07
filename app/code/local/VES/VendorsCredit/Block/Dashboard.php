<?php
class VES_VendorsCredit_Block_Dashboard extends Mage_Core_Block_Template
{
	public function getVendor(){
		return Mage::getSingleton('vendors/session')->getVendor();
	}
	
	public function getCredit(){
		return Mage::helper('vendorscredit')->formatCredit($this->getVendor()->getCredit());
	}
}