<?php
/**
 * Adminhtml sales shipments block
 *
 * @category   	VES
 * @package    	VES_Vendors
 * @author    	Vnecoms Team <support@vnecoms.com>
 */

class VES_VendorsSales_Block_Vendor_Sales_Shipment extends Mage_Adminhtml_Block_Sales_Shipment
{

    public function __construct()
    {
        parent::__construct();
        $this->_controller = 'vendor_sales_shipment';
        $this->_blockGroup = 'vendorssales';
        $this->_removeButton('add');
    }
}
