<?php

/**
 * Catalog manage products block
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsProduct_Block_Adminhtml_Catalog_Product_Pending extends Mage_Adminhtml_Block_Catalog_Product
{
	/**
     * Set template
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('ves_vendorsproduct/product.phtml');
    }
	/**
     * Prepare button and grid
     *
     * @return Mage_Adminhtml_Block_Catalog_Product
     */
    protected function _prepareLayout()
    {
        $this->setChild('grid', $this->getLayout()->createBlock('vendorsproduct/adminhtml_catalog_product_pending_grid', 'product.grid'));
        return Mage_Adminhtml_Block_Widget_Container::_prepareLayout();
    }
}
