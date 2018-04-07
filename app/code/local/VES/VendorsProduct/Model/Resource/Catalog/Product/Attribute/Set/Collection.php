<?php
class VES_VendorsProduct_Model_Resource_Catalog_Product_Attribute_Set_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('vendorsproduct/catalog_product_attribute_set');
    }
}