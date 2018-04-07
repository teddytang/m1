<?php

class VES_Vendors_Model_Mysql4_Item extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the vendors_id refers to the key field in your database table.
        $this->_init('vendors/item', 'item_id');
    }
}