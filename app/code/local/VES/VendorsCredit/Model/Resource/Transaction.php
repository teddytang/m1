<?php

class VES_VendorsCredit_Model_Resource_Transaction extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
    {    
        $this->_init('vendorscredit/transaction', 'transaction_id');
    }
}