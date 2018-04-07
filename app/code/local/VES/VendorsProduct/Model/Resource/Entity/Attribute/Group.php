<?php
class VES_VendorsProduct_Model_Resource_Entity_Attribute_Group extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('vendorsproduct/attribute_group', 'attribute_group_id');
    }
    
    /**
     * Checks if attribute group exists
     *
     * @param VES_VendorsProduct_Model_Entity_Attribute_Group $object
     * @return boolean
     */
    public function itemExists($object)
    {
        $adapter   = $this->_getReadAdapter();
        $bind      = array(
            'attribute_set_id'      => $object->getAttributeSetId(),
            'attribute_group_name'  => $object->getAttributeGroupName()
        );
        $select = $adapter->select()
        ->from($this->getMainTable())
        ->where('attribute_set_id = :attribute_set_id')
        ->where('attribute_group_name = :attribute_group_name');
    
        return $adapter->fetchRow($select, $bind) > 0;
    }
    
    /**
     * Perform actions after object save
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if ($object->getAttributes()) {
            foreach ($object->getAttributes() as $attribute) {
                $attribute->setAttributeGroupId($object->getId());
                $attribute->save();
            }
        }
    
        return parent::_afterSave($object);
    }
}