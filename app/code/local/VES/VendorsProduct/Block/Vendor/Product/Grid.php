<?php
/**
 * Adminhtml customer grid block
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsProduct_Block_Vendor_Product_Grid extends Mage_Adminhtml_Block_Catalog_Product_Grid
{
    public function __construct()
    {
		return parent::__construct();;
    }
	protected function _getStore()
    {
        return Mage::app()->getStore();
    }
	protected function _prepareCollection()
    {
        $vendorId = Mage::getSingleton('vendors/session')->getVendorId();
        $store = $this->_getStore();
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('vendor_sku')
            ->addAttributeToSelect('approval')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id');
        /*Filter by Vendor Id */
		$collection->addFieldToFilter('vendor_id',$vendorId);
		
        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $collection->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');
        }
        if ($store->getId()) {
            //$collection->setStoreId($store->getId());
            $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
            $collection->addStoreFilter($store);
            $collection->joinAttribute(
                'name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'custom_name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'status',
                'catalog_product/status',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'visibility',
                'catalog_product/visibility',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'price',
                'catalog_product/price',
                'entity_id',
                null,
                'left',
                $store->getId()
            );
        }
        else {
            $collection->addAttributeToSelect('price');
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
        }

        $this->setCollection($collection);
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
    	parent::_prepareColumns();
    	$this->getColumn('set_name')->setOptions(Mage::getModel('vendorsproduct/source_attributeset')->getVendorOptionArray());
    	$this->getColumn('type')->setOptions(Mage::getModel('vendorsproduct/source_type')->getVendorOptionArray());
    	$this->removeColumn('websites');
    	$this->removeColumn('sku');
		
		$this->getColumn('price')->setData("display_currency_select",false);
		 //$store = $this->_getStore();
		// echo $store->getId();exit;
    	$this->addColumnAfter('approval',
            array(
                'header'=> Mage::helper('catalog')->__('Approval'),
                'index' => 'approval',
            	'type'  => 'options',
            	'width'	=> '100px',
                'options' 	=> Mage::getSingleton('vendorsproduct/source_approval')->getOptionArray(),
            	'renderer'	=> new VES_VendorsProduct_Block_Widget_Grid_Column_Renderer_Approval(),
        ),'status');
        
        $renderer = new VES_VendorsProduct_Block_Widget_Grid_Column_Renderer_Image();
        $renderer->setVendor(Mage::getSingleton('vendors/session')->getVendor());
    	$this->addColumnAfter('thumbnail',
            array(
                'header'=> Mage::helper('catalog')->__('Thumbnail'),
                'index' => 'entity_id',
            	'type'  => 'text',
            	'width'	=> '100px',
            	'renderer'	=> $renderer,
            	'filter'    => false,
                'sortable'  => false,
        ),'entity_id');
        
    	$this->addColumnAfter('vendor_sku',
    			array(
    					'header'=> Mage::helper('catalog')->__('SKU'),
    					'index' => 'vendor_sku',
    					'type'  => 'text',
    					'width'	=> '100px',
    			),'qty');
				
				
        Mage_Adminhtml_Block_Widget_Grid::_prepareColumns();
    	unset($this->_rssLists);
    	return $this;
    }
    protected function _prepareMassaction()
    {
    	parent::_prepareMassaction();
    	$this->getMassactionBlock()->addItem('approval', array(
             'label'=> Mage::helper('vendorsproduct')->__('Submit For Approval'),
             'url'  => $this->getUrl('*/*/massApproval'),
        ));
		Mage::dispatchEvent('vendors_catalog_product_grid_prepare_massaction', array('block' => $this));
    }
}
