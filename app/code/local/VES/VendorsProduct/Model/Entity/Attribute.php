<?php
class VES_VendorsProduct_Model_Entity_Attribute extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        $this->_init('vendorsproduct/entity_attribute');
    }
    
    /**
     * Delete entity
     *
     * @return Mage_Eav_Model_Resource_Entity_Attribute
     */
    public function deleteEntity()
    {
        return $this->_getResource()->deleteEntity($this);
    }
}
