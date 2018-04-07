<?php
/**
 * Customer edit block
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsProduct_Block_Adminhtml_Catalog_Product_Edit_Tab_Super_Grid extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Attributes
{
    protected function _prepareLayout(){
    	parent::_prepareLayout();
    	$grid = $this->getLayout()->getBlock('admin.product.edit.tab.super.config.grid');
    	if($grid){
    		$grid->addColumnAfter('vendor_sku', array(
	            'header'    => Mage::helper('catalog')->__('Vendor SKU'),
	            'width'     => '80px',
	            'index'     => 'vendor_sku'
	        ),'set_name');
    	}
    }
}
