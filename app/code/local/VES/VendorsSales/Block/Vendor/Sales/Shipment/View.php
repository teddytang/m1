<?php

/**
 * Adminhtml sales shipment view
 *
 * @category   	VES
 * @package    	VES_Vendors
 * @author    	Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsSales_Block_Vendor_Sales_Shipment_View extends Mage_Adminhtml_Block_Template
{
    /**
     * Initialization
     */
    public function _prepareLayout()
    {
        parent::_prepareLayout();
        $shipmentViewBlock = $this->getLayout()->getBlock('sales_shipment_view');
        if($shipmentViewBlock){
	        $shipmentViewBlock->updateButton('save', 'label', Mage::helper('sales')->__('Send Tracking Information'));
			$shipmentViewBlock->updateButton('save',
				'onclick', "deleteConfirm('"
				. Mage::helper('sales')->__('Are you sure you want to send Shipment email to customer?')
				. "', '" . $this->getEmailUrl($shipmentViewBlock->getShipment()) . "')"
			);
        }
    }
    
	public function getEmailUrl(Mage_Sales_Model_Order_Shipment $shipment)
    {
        return $this->getUrl('*/sales_shipment/email', array('shipment_id'  => $shipment->getId()));
    }

}
