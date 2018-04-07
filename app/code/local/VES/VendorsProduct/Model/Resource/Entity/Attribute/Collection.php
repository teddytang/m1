<?php
class VES_VendorsProduct_Model_Resource_Entity_Attribute_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('vendorsproduct/entity_attribute');
    }
}