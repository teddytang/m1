<?php

/**
 * Vendor dashboard recent orders grid
 *
 * @category   	VES
 * @package    	VES_Vendors
 * @author    	Vnecoms Team <support@vnecoms.com>
 */

class VES_VendorsDashboard_Block_Vendor_Dashboard_Orders_Grid extends Mage_Adminhtml_Block_Dashboard_Orders_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('lastOrdersGrid');
    }

    protected function _prepareCollection()
    {
        if (!Mage::helper('core')->isModuleEnabled('Mage_Reports')) {
            return $this;
        }
        $vendorId = Mage::getSingleton('vendors/session')->getVendor()->getId();
        $collection = Mage::getResourceModel('reports/order_collection')->addFieldToFilter('vendor_id',$vendorId)
            ->addItemCountExpr()
            ->joinCustomerName('customer')
            ->orderByCreatedAt();
        if($this->getParam('store') || $this->getParam('website') || $this->getParam('group')) {
            if ($this->getParam('store')) {
                $collection->addAttributeToFilter('store_id', $this->getParam('store'));
            } else if ($this->getParam('website')){
                $storeIds = Mage::app()->getWebsite($this->getParam('website'))->getStoreIds();
                $collection->addAttributeToFilter('store_id', array('in' => $storeIds));
            } else if ($this->getParam('group')){
                $storeIds = Mage::app()->getGroup($this->getParam('group'))->getStoreIds();
                $collection->addAttributeToFilter('store_id', array('in' => $storeIds));
            }

            $collection->addRevenueToSelect();
        } else {
            $collection->addRevenueToSelect(true);
        }

        $this->setCollection($collection);

        return Mage_Adminhtml_Block_Dashboard_Grid::_prepareCollection();
    }


    protected function _prepareColumns()
    {
        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/sales_order/view', array('order_id'=>$row->getId()));
    }
}
