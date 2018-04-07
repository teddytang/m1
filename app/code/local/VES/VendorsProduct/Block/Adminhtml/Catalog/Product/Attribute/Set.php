<?php

/**
 * Catalog manage products block
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsProduct_Block_Adminhtml_Catalog_Product_attribute_Set extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	/**
     * Set template
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_catalog_product_attribute_set';
		$this->_blockGroup = 'vendorsproduct';
		$this->_headerText = Mage::helper('vendorsproduct')->__('Manage Add Product Forms');
		$this->_addButtonLabel = Mage::helper('vendorsproduct')->__('Add New Set');
		parent::__construct();
		$this->removeButton('add');
    }
}
