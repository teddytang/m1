<?php

/**
 * Vendor sales orders block
 *
 * @category   	VES
 * @package    	VES_Vendors
 * @author    	Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsSales_Block_Vendor_Sales_Order extends Mage_Adminhtml_Block_Sales_Order
{

    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'vendorssales';
        $this->_controller = 'vendor_sales_order';
        $this->_removeButton('add');
    }

    public function getCreateUrl()
    {
        return $this->getUrl('*/sales_order_create/start');
    }
}
