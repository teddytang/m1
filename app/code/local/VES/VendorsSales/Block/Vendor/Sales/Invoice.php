<?php

/**
 * Adminhtml sales invoices block
 *
 * @category   	VES
 * @package    	VES_Vendors
 * @author    	Vnecoms Team <support@vnecoms.com>
 */

class VES_VendorsSales_Block_Vendor_Sales_Invoice extends Mage_Adminhtml_Block_Sales_Invoice
{

    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'vendorssales';
        $this->_controller = 'vendor_sales_invoice';
        $this->_removeButton('add');
    }

    public function getPaymentInfoHtml()
    {
        return $this->getChildHtml('payment_info');
    }
}
