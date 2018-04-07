<?php

/**
 * Order history block
 *
 * @category   	VES
 * @package    	VES_Vendors
 * @author    	Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsSales_Block_Vendor_Sales_Order_View_Tab_Shipments extends Mage_Adminhtml_Block_Sales_Order_View_Tab_Shipments
{
	public function getRowUrl($row)
    {
        return $this->getUrl(
            '*/sales_shipment/view',
            array(
                'shipment_id'=> $row->getId(),
                'order_id'  => $row->getOrderId()
             ));
    }	
}
