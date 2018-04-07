<?php
/**
 * Vendor dashboard sales statistics bar
 *
 * @category   	VES
 * @package    	VES_Vendors
 * @author    	Vnecoms Team <support@vnecoms.com>
 */

class VES_VendorsDashboard_Block_Vendor_Dashboard_General_Sales extends Mage_Adminhtml_Block_Dashboard_Sales
{
    protected function _construct()
    {
        parent::_construct();
    }

    protected function _prepareLayout()
    {
        if (!Mage::helper('core')->isModuleEnabled('Mage_Reports')) {
            return $this;
        }
		$vendorId = Mage::getSingleton('vendors/session')->getVendor()->getId();
        $collection = Mage::getResourceModel('sales/order_invoice_item_collection');
        	$collection->getSelect()->columns(array('lifetime'=>'sum(main_table.base_row_total)','grand_total'=>'sum(main_table.row_total)'))
        	//->group('order_id')
        	->join(array('order_item_table'=>$collection->getTable('sales/order_item')),'order_item_id=item_id',array('vendor_id'))
        	//->where('order_table.status = "'.Mage_Sales_Model_Order::STATE_COMPLETE.'"')
        	->where('order_item_table.vendor_id=?',$vendorId);
        $collection->load();
        $sales = $collection->getFirstItem();

        $this->addTotal($this->__('Lifetime Sales'), $sales->getLifetime());
    }
}
