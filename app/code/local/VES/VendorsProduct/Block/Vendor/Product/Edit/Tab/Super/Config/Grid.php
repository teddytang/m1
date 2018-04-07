<?php
/**
 * Customer edit block
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsProduct_Block_Vendor_Product_Edit_Tab_Super_Config_Grid extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_Grid
{
	protected function _prepareCollection()
    {
    	$vendorId = Mage::getSingleton('vendors/session')->getVendor()->getId();
    	if(!$vendorId) return parent::_prepareCollection();
    	
    	$allowProductTypes = array();
        foreach (Mage::helper('catalog/product_configuration')->getConfigurableAllowedTypes() as $type) {
            $allowProductTypes[] = $type->getName();
        }

        $product = $this->_getProduct();
        $collection = $product->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('vendor_sku')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id')
            ->addAttributeToSelect('price')
            ->addFieldToFilter('attribute_set_id',$product->getAttributeSetId())
            ->addFieldToFilter('type_id', $allowProductTypes)
            ->addFilterByRequiredOptions()
            ->joinAttribute('name', 'catalog_product/name', 'entity_id', null, 'inner');
		
        $collection->addAttributeToFilter('vendor_id',$vendorId);
        
        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            Mage::getModel('cataloginventory/stock_item')->addCatalogInventoryToProductCollection($collection);
        }

        foreach ($product->getTypeInstance(true)->getUsedProductAttributes($product) as $attribute) {
            $collection->addAttributeToSelect($attribute->getAttributeCode());
            $collection->addAttributeToFilter($attribute->getAttributeCode(), array('notnull'=>1));
        }
        $this->setCollection($collection);

        if ($this->isReadonly()) {
            $collection->addFieldToFilter('entity_id', array('in' => $this->_getSelectedProducts()));
        }

        Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
        return $this;
    }
    
    protected function _prepareColumns()
    {
    	$vendorId = Mage::getSingleton('vendors/session')->getVendor()->getId();
    	if(!$vendorId) return parent::_prepareColumns();
    	
    	parent::_prepareColumns();
    	$this->addColumnAfter('vendor_sku', array(
            'header'    => Mage::helper('catalog')->__('SKU'),
            'width'     => '80px',
            'index'     => 'vendor_sku'
        ),'set_name');
        $this->removeColumn('sku');
        Mage_Adminhtml_Block_Widget_Grid::_prepareColumns();
        return $this;
    }
}
