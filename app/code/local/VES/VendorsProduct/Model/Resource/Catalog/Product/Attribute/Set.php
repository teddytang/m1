<?php
class VES_VendorsProduct_Model_Resource_Catalog_Product_Attribute_Set extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('vendorsproduct/attribute_set', 'attribute_set_id');
    }
    
    /**
     * Perform actions after object save
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Set
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if ($object->getGroups()) {
            /* @var $group VES_VendorsProduct_Model_Entity_Attribute_Group */
            foreach ($object->getGroups() as $group) {
                $group->setAttributeSetId($object->getId());
                if ($group->itemExists() && !$group->getId()) {
                    continue;
                }
                $group->save();
            }
        }
        if ($object->getRemoveGroups()) {
            foreach ($object->getRemoveGroups() as $group) {
                /* @var $group VES_VendorsProduct_Model_Entity_Attribute_Group */
                $group->delete();
            }
            //Mage::getResourceModel('eav/entity_attribute_group')->updateDefaultGroup($object->getId());
        }
        if ($object->getRemoveAttributes()) {
            foreach ($object->getRemoveAttributes() as $attribute) {
                /* @var $attribute Mage_Eav_Model_Entity_Attribute */
                $attribute->deleteEntity();
            }
        }
    
        return parent::_afterSave($object);
    }
    
    /**
     * Validate attribute set name
     *
     * @param Mage_Eav_Model_Entity_Attribute_Set $object
     * @param string $attributeSetName
     * @return bool
     */
    public function validate($object, $attributeSetName)
    {
        return true;
    }
}