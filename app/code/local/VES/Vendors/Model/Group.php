<?php

class VES_Vendors_Model_Group extends Mage_Core_Model_Abstract
{
    const NOT_LOGGED_IN_ID          = 0;
    const XML_PATH_DEFAULT_ID       = 'vendors/create_account/default_group';
    
    const CALCULATE_FEE_BY_FIXED_AMOUNT						= 0;
    const CALCULATE_FEE_BY_PERCENT_AMOUNT					= 1;
    const CALCULATE_FEE_BY_PERCENT_SUBTOTAL_AMOUNT			= 2;
    const CALCULATE_FEE_BY_PERCENT_SUBTOTAL_AFTER_DISCOUNT	= 3;
    const CALCULATE_FEE_BY_ITEM_ROWTOTAL_AMOUNT				= 4;
    
    protected $_eventPrefix 	= 'vendor_group';
    protected $_eventObject		= 'vendor_group';
    
	public function _construct()
    {
        parent::_construct();
        $this->_init('vendors/group');
    }
    
    public function getName(){
    	return $this->getVendorGroupCode();
    }
    
    /*Get fee amount*/
    public function getFeeAmount($amount){
    	if(!$this->getId()) throw new Mage_Core_Exception(Mage::helper('vendors')->__('The vendor group does not exist'));
    	
    	switch($this->getFeeBy()){
    		case self::CALCULATE_FEE_BY_FIXED_AMOUNT:
    			return $this->getFee();
    			break;
    		case self::CALCULATE_FEE_BY_PERCENT_AMOUNT:
    			return ($this->getFee() * $amount) / 100;
    			break;
    	}
    }
}