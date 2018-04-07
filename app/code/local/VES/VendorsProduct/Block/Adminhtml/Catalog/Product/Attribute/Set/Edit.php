<?php

/**
 * Edit vendor product attribute set
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsProduct_Block_Adminhtml_Catalog_Product_Attribute_Set_Edit extends Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Main
{
    /**
     * Initialize template
     *
     */
    protected function _construct()
    {
        $this->setTemplate('ves_vendorsproduct/product/attribute/set/main.phtml');
    }
    public function getVendorSet(){
        return Mage::registry('vendor_attribute_set');
    }
    /**
     * Prepare Global Layout
     *
     * @return Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Main
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->unsetChild('edit_set_form');
        $this->setChild('edit_set_form',
            $this->getLayout()->createBlock('vendorsproduct/adminhtml_catalog_product_attribute_set_main_formset')
        );
        $this->getChild('save_button')->setData('label',Mage::helper('vendorsproduct')->__('Save Product Form'));
        $this->getChild('delete_button')->setData('label',Mage::helper('vendorsproduct')->__('Delete Product Form'));
        
        return $this;
    }
    /**
     * Retrieve Attribute Set Group Tree as JSON format
     *
     * @return string
     */
    public function getNewGroupTreeJson()
    {
        $items = array();
        $setId = $this->_getSetId();
    
        /* @var $groups Mage_Eav_Model_Mysql4_Entity_Attribute_Group_Collection */
        $groups = Mage::getModel('eav/entity_attribute_group')
        ->getResourceCollection()
        ->setAttributeSetFilter($setId)
        ->setSortOrder()
        ->load();
    
        $configurable = Mage::getResourceModel('catalog/product_type_configurable_attribute')
        ->getUsedAttributes($setId);
        $requiredAttributes = Mage::helper('vendorsproduct')->getRequiredProductAttributes();
        /* @var $node Mage_Eav_Model_Entity_Attribute_Group */
        foreach ($groups as $node) {
            $item = array();
            $item['text']       = $node->getAttributeGroupName();
            $item['id']         = $node->getAttributeGroupId();
            $item['cls']        = 'folder';
            $item['allowDrop']  = true;
            $item['allowDrag']  = true;
    
            $nodeChildren = Mage::getResourceModel('catalog/product_attribute_collection')
            ->setAttributeGroupFilter($node->getId())
            ->addVisibleFilter()
            ->checkConfigurableProducts()
            ->load();
    
            if ($nodeChildren->getSize() > 0) {
                $item['children'] = array();
                foreach ($nodeChildren->getItems() as $child) {
                    $isRequiredAttr = in_array($child->getAttributeCode(), $requiredAttributes);
                    /* @var $child Mage_Eav_Model_Entity_Attribute */
                    $attr = array(
                        'text'              => $child->getAttributeCode(),
                        'id'                => $child->getAttributeId(),
                        'cls'               => $isRequiredAttr ? 'system-leaf' : 'leaf',
                        'allowDrop'         => false,
                        'allowDrag'         => true,
                        'leaf'              => true,
                        'is_user_defined'   => !$isRequiredAttr,
                        'is_configurable'   => (int)in_array($child->getAttributeId(), $configurable),
                        'entity_id'         => $child->getEntityAttributeId()
                    );
    
                    $item['children'][] = $attr;
                }
            }
    
            $items[] = $item;
        }
    
        return Mage::helper('core')->jsonEncode($items);
    }
    /**
     * Retrieve Attribute Set Group Tree as JSON format
     *
     * @return string
     */
    public function getGroupTreeJson()
    {
        if(!$this->getVendorSet()->getId()) {
            return $this->getNewGroupTreeJson();
        }
        
        $items = array();
        $setId = $this->getVendorSet()->getId();
    
        /* @var $groups Mage_Eav_Model_Mysql4_Entity_Attribute_Group_Collection */
        $groups = Mage::getModel('vendorsproduct/entity_attribute_group')
        ->getCollection()
        ->addFieldToFilter('attribute_set_id',$setId)
        ->setOrder('sort_order','ASC')
        ->load();
        
        $requiredAttributes = Mage::helper('vendorsproduct')->getRequiredProductAttributes();
    
        /* @var $node Mage_Eav_Model_Entity_Attribute_Group */
        foreach ($groups as $node) {
            $item = array();
            $item['text']       = $node->getAttributeGroupName();
            $item['id']         = $node->getAttributeGroupId();
            $item['cls']        = 'folder';
            $item['allowDrop']  = true;
            $item['allowDrag']  = true;
    
            $nodeChildren = Mage::getResourceModel('vendorsproduct/catalog_product_attribute_collection')
            ->setAttributeGroupFilter($node->getId())
            ->addVisibleFilter()
            ->checkConfigurableProducts()
            ->load();
    
            if ($nodeChildren->getSize() > 0) {
                $item['children'] = array();
                foreach ($nodeChildren->getItems() as $child) {
                    $isRequiredAttr = in_array($child->getAttributeCode(), $requiredAttributes);
                    /* @var $child Mage_Eav_Model_Entity_Attribute */
                    $attr = array(
                        'text'              => $child->getAttributeCode(),
                        'id'                => $child->getAttributeId(),
                        'cls'               => $isRequiredAttr ? 'system-leaf' : 'leaf',
                        'allowDrop'         => false,
                        'allowDrag'         => true,
                        'leaf'              => true,
                        'is_user_defined'   => !$isRequiredAttr,
                        'is_configurable'   => false,
                        'entity_id'         => $child->getEntityAttributeId()
                    );
    
                    $item['children'][] = $attr;
                }
            }
    
            $items[] = $item;
        }
    
        return Mage::helper('core')->jsonEncode($items);
    }
    
    
    /**
     * Retrieve Unused in Attribute Set Attribute Tree as JSON
     *
     * @return string
     */
    public function getAttributeTreeJson()
    {
        $items = array();
        if(!$this->getVendorSet()->getId()) {
            $items[] = array(
                'text'      => Mage::helper('catalog')->__('Empty'),
                'id'        => 'empty',
                'cls'       => 'folder',
                'allowDrop' => false,
                'allowDrag' => false,
            );
            return Mage::helper('core')->jsonEncode($items);
        }
        
        $setId = $this->getVendorSet()->getId();
               
        $collection = Mage::getResourceModel('vendorsproduct/catalog_product_attribute_collection')
        ->setVendorAttributeSetFilter($setId)
        ->load();

        $attributesIds = array('0');
        /* @var $item Mage_Eav_Model_Entity_Attribute */
        foreach ($collection->getItems() as $item) {
            $attributesIds[] = $item->getAttributeId();
        }
        
        $noteAllowedAttributes = Mage::helper('vendorsproduct')->getRestrictionProductAttribute();
        
        $attributes = Mage::getResourceModel('vendorsproduct/catalog_product_attribute_collection')
        ->setAttributeSetFilter($this->_getSetId())
        ->setAttributesExcludeFilter($attributesIds)
        ->setAttributeCodesExcludeFilter($noteAllowedAttributes)
        ->addVisibleFilter()
        ->load();
        $requiredAttributes = Mage::helper('vendorsproduct')->getRequiredProductAttributes();
        foreach ($attributes as $child) {
            $isRequiredAttr = in_array($child->getAttributeCode(), $requiredAttributes);
            $attr = array(
                'text'              => $child->getAttributeCode(),
                'id'                => $child->getAttributeId(),
                'cls'               => $isRequiredAttr ? 'system-leaf' : 'leaf',
                'allowDrop'         => false,
                'allowDrag'         => true,
                'leaf'              => true,
                'is_user_defined'   => !$isRequiredAttr,
                'is_configurable'   => false,
                'entity_id'         => $child->getEntityId()
            );
    
            $items[] = $attr;
        }
    
        if (count($items) == 0) {
            $items[] = array(
                'text'      => Mage::helper('catalog')->__('Empty'),
                'id'        => 'empty',
                'cls'       => 'folder',
                'allowDrop' => false,
                'allowDrag' => false,
            );
        }
    
        return Mage::helper('core')->jsonEncode($items);
    }
    
    /**
     * Retrieve Attribute Set Save URL
     *
     * @return string
     */
    public function getMoveUrl()
    {
        return $this->getUrl('*/vendors_catalog_product_set/save', array('id'=>$this->getVendorSet()->getId(),'parent_id' => $this->_getSetId()));
    }
}
