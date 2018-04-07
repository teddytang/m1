<?php

class VES_VendorsCredit_Model_Source_Vendor
{
	public function getAllOptions(){
		$vendors = Mage::getModel('vendors/vendor')->getCollection()->addOrder('vendor_id','ASC');
		$options = array(''=>Mage::helper('vendorscredit')->__('-- Select Vendor --'));
		foreach($vendors as $vendor){
			$options[$vendor->getId()]	= $vendor->getVendorId();
		}
		return $options;
	}
}