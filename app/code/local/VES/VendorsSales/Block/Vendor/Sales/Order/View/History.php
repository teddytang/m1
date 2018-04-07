<?php

/**
 * Order history block
 *
 * @category   	VES
 * @package    	VES_Vendors
 * @author    	Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsSales_Block_Vendor_Sales_Order_View_History extends Mage_Adminhtml_Block_Sales_Order_View_History
{
	public function canAddComment()
    {
        return $this->getOrder()->canComment() && Mage::getStoreConfig('vendors/sales/send_order_comments');
    }

}
