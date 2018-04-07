<?php
class VES_VendorsCommissionCalculation_Model_Resource_Rule_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	protected function _construct()
    {
        $this->_init('vendorscommission/rule');
    }
}