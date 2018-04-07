<?php

class VES_VendorsProduct_Block_Vendor_Product_Edit_Tab_Super_Group extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Group
{
	public function __construct()
    {
        parent::__construct();
        $this->setId('super_product_grid');
        $this->setDefaultSort('entity_id');
        $this->setSkipGenerateContent(true);
        $this->setUseAjax(true);
        if ($this->_getProduct()->getId()) {
            $this->setDefaultFilter(array('in_products'=>1));
        }
    }
    /**
     * Prepare collection
     *
     * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Group
     */
    protected function _prepareCollection()
    {
        $allowProductTypes = array();
        $allowProductTypeNodes = Mage::getConfig()
            ->getNode('global/catalog/product/type/grouped/allow_product_types')->children();
        foreach ($allowProductTypeNodes as $type) {
            $allowProductTypes[] = $type->getName();
        }

        $collection = Mage::getModel('catalog/product_link')->useGroupedLinks()
            ->getProductCollection()
            ->setProduct($this->_getProduct())
            ->addAttributeToSelect('*')
            ->addFilterByRequiredOptions()
            ->addAttributeToFilter('type_id', $allowProductTypes)
            ->addAttributeToFilter('vendor_id', Mage::getSingleton('vendors/session')->getVendorId());

        if ($this->getIsReadonly() === true) {
            $collection->addFieldToFilter('entity_id', array('in' => $this->_getSelectedProducts()));
        }
        $this->setCollection($collection);
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
    	parent::_prepareColumns();
    	$this->addColumnAfter('vendor_sku', array(
            'header'    => Mage::helper('catalog')->__('SKU'),
            'index'     => 'vendor_sku'
        ),'name');
        $this->removeColumn('sku');
        Mage_Adminhtml_Block_Widget_Grid::_prepareColumns();
    }
}
