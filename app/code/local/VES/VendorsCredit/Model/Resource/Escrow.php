<?php

class VES_VendorsCredit_Model_Resource_Escrow extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
    {    
        $this->_init('vendorscredit/escrow', 'escrow_id');
    }
}