<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog Layer Price Filter resource model
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class VES_VendorsProduct_Model_Resource_Layer_Filter_Price extends Mage_Catalog_Model_Resource_Layer_Filter_Price
{

    /**
     * Retrieve clean select with joined price index table
     *
     * @param Mage_Catalog_Model_Layer_Filter_Price $filter
     * @return Varien_Db_Select
     */
    protected function _getSelect($filter)
    {
        $collection = $filter->getLayer()->getProductCollection();
        $collection->addPriceData($filter->getCustomerGroupId(), $filter->getWebsiteId());

        if (!is_null($collection->getCatalogPreparedSelect())) {
            $select = clone $collection->getCatalogPreparedSelect();
        } else {
            $select = clone $collection->getSelect();
        }

        // reset columns, order and limitation conditions
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);

        //$select->joinTable(array('catalog_tbl'=>'catalog/product'), 'entity_id=entity_id',array('approval'=>'approval'));
        // remove join with main table
        $fromPart = $select->getPart(Zend_Db_Select::FROM);
        if (!isset($fromPart[Mage_Catalog_Model_Resource_Product_Collection::INDEX_TABLE_ALIAS])
            || !isset($fromPart[Mage_Catalog_Model_Resource_Product_Collection::MAIN_TABLE_ALIAS])
        ) {
            return $select;
        }

        // processing FROM part
        $priceIndexJoinPart = $fromPart[Mage_Catalog_Model_Resource_Product_Collection::INDEX_TABLE_ALIAS];
        $priceIndexJoinConditions = explode('AND', $priceIndexJoinPart['joinCondition']);
        $priceIndexJoinPart['joinType'] = Zend_Db_Select::FROM;
        $priceIndexJoinPart['joinCondition'] = null;
        $fromPart[Mage_Catalog_Model_Resource_Product_Collection::MAIN_TABLE_ALIAS] = $priceIndexJoinPart;
        unset($fromPart[Mage_Catalog_Model_Resource_Product_Collection::INDEX_TABLE_ALIAS]);
        $select->setPart(Zend_Db_Select::FROM, $fromPart);
        foreach ($fromPart as $key => $fromJoinItem) {
            $fromPart[$key]['joinCondition'] = $this->_replaceTableAlias($fromJoinItem['joinCondition']);
        }
        $select->setPart(Zend_Db_Select::FROM, $fromPart);

        // processing WHERE part
        $wherePart = $select->getPart(Zend_Db_Select::WHERE);
        $excludedWherePart = Mage_Catalog_Model_Resource_Product_Collection::MAIN_TABLE_ALIAS . '.status';
        foreach ($wherePart as $key => $wherePartItem) {
            if (strpos($wherePartItem, $excludedWherePart) !== false) {
                $wherePart[$key] = new Zend_Db_Expr('1=1');
                continue;
            }
            $wherePart[$key] = $this->_replaceTableAlias($wherePartItem);
        }
        $select->setPart(Zend_Db_Select::WHERE, $wherePart);
        $excludeJoinPart = Mage_Catalog_Model_Resource_Product_Collection::MAIN_TABLE_ALIAS . '.entity_id';
        foreach ($priceIndexJoinConditions as $condition) {
            if (strpos($condition, $excludeJoinPart) !== false) {
                continue;
            }
            $select->where($this->_replaceTableAlias($condition));
        }
        $select->where($this->_getPriceExpression($filter, $select) . ' IS NOT NULL');

        if (Mage::helper('catalog/category_flat')->isEnabled()) {
            $select->columns('entity_id');
            $resource = Mage::getSingleton('core/resource');
            $ticketTable = $resource->getTableName('catalog/product_flat'). '_' . $filter->getStoreId();;
            $select->joinLeft(array("catalog" => $ticketTable), "e.entity_id = catalog.entity_id", array("e.approval" => "catalog.approval", "e.vendor_id" => "catalog.vendor_id"));
        }
		else{
			$resource = Mage::getSingleton('core/resource');
			$attributeCode = "approval";
			$alias     = $attributeCode . '_table';
            $attribute = Mage::getSingleton('eav/config')
                ->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attributeCode);
				
			$select->joinLeft(
                array($alias => $attribute->getBackendTable()),
                "e.entity_id = $alias.entity_id AND $alias.attribute_id={$attribute->getId()}",
                array($attributeCode => 'value')
            );
			$ticketTable = $resource->getTableName('catalog/product');
            $select->joinLeft(array("catalog" => $ticketTable), "e.entity_id = catalog.entity_id", array("e.vendor_id" => "catalog.vendor_id"));
		}
        return $select;
    }

}
