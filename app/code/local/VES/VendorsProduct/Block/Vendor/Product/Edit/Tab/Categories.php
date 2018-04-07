<?php
/**
 * product categories.
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsProduct_Block_Vendor_Product_Edit_Tab_Categories extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Categories
{
    /**
     * Get JSON of a tree node or an associative array
     *
     * @param Varien_Data_Tree_Node|array $node
     * @param int $level
     * @return string
     */
    protected function _getNodeJson($node, $level = 0)
    {
        // create a node from data array
        if (is_array($node)) {
            $node = new Varien_Data_Tree_Node($node, 'entity_id', new Varien_Data_Tree);
        }
    
        $item = array();
        $item['text'] = $this->buildNodeName($node);
    
        /* $rootForStores = Mage::getModel('core/store')
         ->getCollection()
        ->loadByCategoryIds(array($node->getEntityId())); */
        $rootForStores = in_array($node->getEntityId(), $this->getRootIds());
    
        $item['id']  = $node->getId();
        $item['store']  = (int) $this->getStore()->getId();
        $item['path'] = $node->getData('path');
        /*Disable the node if it's not allowed for vendor to choose.*/
        $disableVendor = Mage::getModel('catalog/category',array('disable_flat'=>true))->getResource()->getAttributeRawValue($node->getId(),'disable_vendor',$this->getStore()->getId());
        if($disableVendor) $item['disabled'] = true;
        
        $item['cls'] = 'folder ' . ($node->getIsActive() ? 'active-category' : 'no-active-category');
        
        //$item['allowDrop'] = ($level<3) ? true : false;
        $allowMove = $this->_isCategoryMoveable($node);
        $item['allowDrop'] = $allowMove;
        // disallow drag if it's first level and category is root of a store
        $item['allowDrag'] = $allowMove && (($node->getLevel()==1 && $rootForStores) ? false : true);
    
        if ((int)$node->getChildrenCount()>0) {
            $item['children'] = array();
        }
    
        $isParent = $this->_isParentSelectedCategory($node);
    
        if ($node->hasChildren()) {
            $item['children'] = array();
            if (!($this->getUseAjax() && $node->getLevel() > 1 && !$isParent)) {
                foreach ($node->getChildren() as $child) {
                    $item['children'][] = $this->_getNodeJson($child, $level+1);
                }
            }
        }
    
        if ($isParent || $node->getLevel() < 2) {
            $item['expanded'] = true;
        }
        
        if ($this->_isParentSelectedCategory($node)) {
            $item['expanded'] = true;
        }
        
        if (in_array($node->getId(), $this->getCategoryIds())) {
            $item['checked'] = true;
        }
        
        if ($this->isReadonly()) {
            $item['disabled'] = true;
        }

        return $item;
    }
    
    public function getCategoryCollection()
    {
        $storeId = $this->getRequest()->getParam('store', Mage::app()->getStore()->getId());
        $collection = $this->getData('category_collection');
        if (is_null($collection)) {
            $collection = Mage::getModel('catalog/category',array('disable_flat'=>true))->getCollection();
    
            /* @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection */
            $collection->addAttributeToSelect('name')
            ->addAttributeToSelect('is_active')
            ->setProductStoreId($storeId)
            ->setLoadProductCount($this->_withProductCount)
            ->setStoreId($storeId);
            $this->setData('category_collection', $collection);
        }
        return $collection;
    }
}
