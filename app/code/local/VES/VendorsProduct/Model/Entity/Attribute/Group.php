<?php
class VES_VendorsProduct_Model_Entity_Attribute_Group extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        $this->_init('vendorsproduct/entity_attribute_group');
    }
    
    /**
     * Checks if current attribute group exists
     *
     * @return boolean
     */
    public function itemExists()
    {
        return $this->_getResource()->itemExists($this);
    }
}
