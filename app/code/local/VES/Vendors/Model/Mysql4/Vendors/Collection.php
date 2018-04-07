<?php

class VES_Vendors_Model_Mysql4_Vendors_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('vendors/vendors');
    }
}