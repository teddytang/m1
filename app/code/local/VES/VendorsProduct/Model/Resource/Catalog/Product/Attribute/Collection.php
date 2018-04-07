<?php
class VES_VendorsProduct_Model_Resource_Catalog_Product_Attribute_Collection extends Mage_Catalog_Model_Resource_Product_Attribute_Collection
{
    /**
     * Filter by attribute group id
     *
     * @param int $groupId
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Collection
     */
    public function setAttributeGroupFilter($groupId)
    {
        $this->getSelect()->join(
            array('entity_attribute'=>$this->getTable('vendorsproduct/entity_attribute')),
            'entity_attribute.attribute_id = main_table.attribute_id'
        );
        $this->addFieldToFilter('entity_attribute.attribute_group_id', $groupId);
        $this->setOrder('sort_order', self::SORT_ORDER_ASC);
    
        return $this;
    }
    /**
     * Exclude attributes filter
     *
     * @param array $attributes
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Collection
     */
    public function setAttributeCodesExcludeFilter($attributeCodes){
        return $this->addFieldToFilter('main_table.attribute_code', array('nin' => $attributeCodes));
    }
    /**
     * Specify attribute set filter
     *
     * @param int $setId
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Collection
     */
    public function setVendorAttributeSetFilter($setId)
    {
        if (is_array($setId)) {
            if (!empty($setId)) {
                $this->getSelect()->join(
                    array('entity_attribute'=>$this->getTable('vendorsproduct/entity_attribute')),
                    'entity_attribute.attribute_id = main_table.attribute_id',
                    'attribute_id'
                );
                $this->addFieldToFilter('entity_attribute.attribute_set_id', array('in' => $setId));
                $this->addAttributeGrouping();
                $this->_useAnalyticFunction = true;
            }
        } elseif ($setId) {
            $this->getSelect()->join(
                array('entity_attribute'=>$this->getTable('vendorsproduct/entity_attribute')),
                'entity_attribute.attribute_id = main_table.attribute_id'
            );
            $this->addFieldToFilter('entity_attribute.attribute_set_id', $setId);
            $this->setOrder('sort_order', self::SORT_ORDER_ASC);
        }
    
        return $this;
    }
}