<?php

/**
 * Catalog manage products block
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <core@magentocommerce.com>
 */
class VES_VendorsProduct_Block_Vendor_Product extends Mage_Adminhtml_Block_Catalog_Product
{
    /**
     * Set template
     */
    public function __construct()
    {
        parent::__construct();
    }
	/**
     * Prepare button and grid
     *
     * @return Mage_Adminhtml_Block_Catalog_Product
     */
    protected function _prepareLayout()
    {
        $this->_addButton('add_new', array(
            'label'   => Mage::helper('catalog')->__('Add Product'),
            'onclick' => "setLocation('{$this->getUrl('*/*/new')}')",
            'class'   => 'add'
        ));
		parent::_prepareLayout();
		$this->setChild('grid', $this->getLayout()->createBlock('vendorsproduct/vendor_product_grid', 'vendor.product.grid'));
    }
}
