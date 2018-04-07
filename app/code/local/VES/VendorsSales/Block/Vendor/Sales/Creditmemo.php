<?php

/**
 * Adminhtml sales creditmemos block
 *
 * @category   	VES
 * @package    	VES_Vendors
 * @author    	Vnecoms Team <support@vnecoms.com>
 */

class VES_VendorsSales_Block_Vendor_Sales_Creditmemo extends Mage_Adminhtml_Block_Sales_Creditmemo
{

    public function __construct()
    {
        parent::__construct();
        $this->_controller = 'vendor_sales_creditmemo';
        $this->_blockGroup = 'vendorssales';
        $this->_removeButton('add');
    }

}
