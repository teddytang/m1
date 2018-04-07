<?php
class VES_VendorsProduct_Model_Catalog_Product_Attribute_Set extends Mage_Core_Model_Abstract
{
    CONST DISPLAY_TYPE_FIELDSETS    = 'fieldsets';
    CONST DISPLAY_TYPE_TABS         = 'tabs';
    public function _construct()
    {
        parent::_construct();
        $this->_init('vendorsproduct/catalog_product_attribute_set');
    }
    
    /**
     * Collect data for save
     *
     * @param array $data
     * @return Mage_Eav_Model_Entity_Attribute_Set
     */
    public function organizeData($data)
    {
        $modelGroupArray = array();
        $modelAttributeArray = array();
        $attributeIds = array();
        if ($data['attributes']) {
            $ids = array();
            foreach ($data['attributes'] as $attribute) {
                $ids[] = $attribute[0];
            }
            $attributeIds = Mage::getResourceSingleton('eav/entity_attribute')
            ->getValidAttributeIds($ids);
        }
        $isNewObj = $this->getIsNewObject();
        if( $data['groups'] ) {
            foreach ($data['groups'] as $group) {
                $modelGroup = Mage::getModel('vendorsproduct/entity_attribute_group');
                if(!$isNewObj){
                    $modelGroup->setId(is_numeric($group[0]) && $group[0] > 0 ? $group[0] : null);
                }
                $modelGroup->setAttributeGroupName($group[1])
                ->setAttributeSetId($this->getId())
                ->setSortOrder($group[2]);
    
                if( $data['attributes'] ) {
                    foreach( $data['attributes'] as $attribute ) {
                        if( $attribute[1] == $group[0] && in_array($attribute[0], $attributeIds) ) {
                            /*Check if the entity attribute is already exist*/
                            $entityAttributeCollection = Mage::getModel('vendorsproduct/entity_attribute')->getCollection()
                            ->addFieldToFilter('attribute_id',$attribute[0])
                            ->addFieldToFilter('attribute_group_id',$attribute[1])
                            ->addFieldToFilter('attribute_set_id',$this->getId())
                            ;
                            $modelAttribute = Mage::getModel('vendorsproduct/entity_attribute');
                            
                            if($attribute[3]){
                                $modelAttribute->load($attribute[3]);
                            }

                            $modelAttribute->setAttributeId($attribute[0])
                            ->setAttributeGroupId($attribute[1])
                            ->setAttributeSetId($this->getId())
                            ->setEntityTypeId($this->getEntityTypeId())
                            ->setSortOrder($attribute[2]);
                            $modelAttributeArray[] = $modelAttribute;
                        }
                    }
                    $modelGroup->setAttributes($modelAttributeArray);
                    $modelAttributeArray = array();
                }
                $modelGroupArray[] = $modelGroup;
            }
            $this->setGroups($modelGroupArray);
        }
    
    
        if( $data['not_attributes'] ) {
            $modelAttributeArray = array();
            foreach( $data['not_attributes'] as $attributeId ) {
                $modelAttribute = Mage::getModel('vendorsproduct/entity_attribute');
    
                $modelAttribute->setEntityAttributeId($attributeId);
                $modelAttributeArray[] = $modelAttribute;
            }
            $this->setRemoveAttributes($modelAttributeArray);
        }
    
        if( $data['removeGroups'] ) {
            $modelGroupArray = array();
            foreach( $data['removeGroups'] as $groupId ) {
                $modelGroup = Mage::getModel('vendorsproduct/entity_attribute_group');
                $modelGroup->setId($groupId);
    
                $modelGroupArray[] = $modelGroup;
            }
            $this->setRemoveGroups($modelGroupArray);
        }
        $this->setAttributeSetName($data['attribute_set_name'])
        ->setEntityTypeId($this->getEntityTypeId());
    
        return $this;
    }
    
    /**
     * Validate attribute set name
     *
     * @param string $name
     * @throws Mage_Eav_Exception
     * @return bool
     */
    public function validate()
    {
        if (!$this->_getResource()->validate($this, $this->getAttributeSetName())) {
            throw Mage::exception('Mage_Eav',
                Mage::helper('eav')->__('Attribute set with the "%s" name already exists.', $this->getAttributeSetName())
            );
        }
    
        return true;
    }
}