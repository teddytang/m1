<?php

/**
 * Vendor sales orders block
 *
 * @category   	VES
 * @package    	VES_Vendors
 * @author    	Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsSales_Block_Adminhtml_Sales_Order extends Mage_Core_Block_Template
{

    protected function _prepareLayout(){
    	parent::_prepareLayout();
     	/*adminhtml_sales_order_index*/
    	$productList = $this->getLayout()->getBlock('sales_order.grid.container');
    	if($productList){
	        $grid = $productList->getChild('grid');
	        $grid->addColumnAfter('vendor_id',
	            array(
	                'header'=> Mage::helper('vendors')->__('Vendor Id'),
	                'index' => 'vendor_id',
	            	'renderer'	=> new VES_VendorsSales_Block_Widget_Grid_Column_Renderer_Text(),
	        	),
	        'real_order_id');
    	}
    	
    	/*adminhtml_sales_order_grid*/
    	$grid = $this->getLayout()->getBlock('sales_order.grid');
    	if($grid){
	        $grid->addColumnAfter('vendor_id',
	            array(
	                'header'=> Mage::helper('vendors')->__('Vendor Id'),
	                'index' => 'vendor_id',
	            	'renderer'	=> new VES_VendorsSales_Block_Widget_Grid_Column_Renderer_Text(),
	        	),
	        'real_order_id');
    	}
    	return $this;
    }
}
