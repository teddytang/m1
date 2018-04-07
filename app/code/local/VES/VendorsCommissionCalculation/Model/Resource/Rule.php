<?php

class VES_VendorsCommissionCalculation_Model_Resource_Rule extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
    {    
        $this->_init('vendorscommission/rule', 'rule_id');
    }
}