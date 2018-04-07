<?php
class VES_VendorsProduct_Model_Resource_Entity_Attribute extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('vendorsproduct/entity_attribute','entity_attribute_id');
    }
    
    /**
     * Load by given attributes ids and return only exist attribute ids
     *
     * @param array $attributeIds
     * @return array
     */
    public function getValidAttributeIds($attributeIds)
    {
        $adapter   = $this->_getReadAdapter();
        $select    = $adapter->select()
        ->from($this->getMainTable(), array('attribute_id'))
        ->where('attribute_id IN (?)', $attributeIds);
    
        return $adapter->fetchCol($select);
    }
    
    /**
     * Delete entity
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Eav_Model_Resource_Entity_Attribute
     */
    public function deleteEntity(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getEntityAttributeId()) {
            return $this;
        }
    
        $this->_getWriteAdapter()->delete($this->getTable('vendorsproduct/entity_attribute'), array(
            'entity_attribute_id = ?' => $object->getEntityAttributeId()
        ));
    
        return $this;
    }
}
