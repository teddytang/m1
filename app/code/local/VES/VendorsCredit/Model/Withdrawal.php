<?php

class VES_VendorsCredit_Model_Withdrawal extends Mage_Core_Model_Abstract
{
	const STATUS_PENDING	= 0;
	const STATUS_COMPLETE	= 1;
	const STATUS_CANCELED	= 2;
	
	protected $_vendor;
	public function _construct()
    {
        parent::_construct();
        $this->_init('vendorscredit/withdrawal');
    }
    
    public function getVendor(){
    	if(!$this->_vendor){
    		$this->_vendor = Mage::getModel('vendors/vendor')->load($this->getVendorId());
    	}
    	return $this->_vendor;
    }
}