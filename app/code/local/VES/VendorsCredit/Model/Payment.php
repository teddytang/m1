<?php

class VES_VendorsCredit_Model_Payment extends Mage_Core_Model_Abstract
{

	public function _construct()
    {
        parent::_construct();
        $this->_init('vendorscredit/payment');
    }
}