<?php

/**
 * Catalog manage products block
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <core@magentocommerce.com>
 */
class VES_VendorsProduct_Block_Adminhtml_Catalog_Product extends Mage_Core_Block_Template
{
	/**
     * Add vendor id to product grid
     *
     * @return VES_VendorsCatalog_Block_Adminhtml_Catalog_Product
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        
        /*adminhtml_catalog_product_index*/
    	$productList = $this->getLayout()->getBlock('products_list');
    	if($productList){
	        $grid = $productList->getChild('grid');
	        $grid->addColumnAfter('vendor_id',
	            array(
	                'header'=> Mage::helper('vendors')->__('Vendor Id'),
	                'index' => 'vendor_id',
	            	'renderer'	=> new VES_VendorsProduct_Block_Widget_Grid_Column_Renderer_Text(),
	        	),
	        'entity_id');
    	}
    	
    	/*adminhtml_catalog_product_grid*/
    	$grid = $this->getLayout()->getBlock('admin.product.grid');
    	if($grid){
	        $grid->addColumnAfter('vendor_id',
	            array(
	                'header'=> Mage::helper('vendors')->__('Vendor Id'),
	                'index' => 'vendor_id',
	            	'renderer'	=> new VES_VendorsProduct_Block_Widget_Grid_Column_Renderer_Text(),
	        	),
	        'entity_id');
    	}
    }
}
