<?php

/**
 * Vendor dashboard recent orders grid
 *
 * @category   	VES
 * @package    	VES_Vendors
 * @author    	Vnecoms Team <support@vnecoms.com>
 */

class VES_VendorsDashboard_Block_Vendor_Dashboard_General_Items_Grid extends Mage_Adminhtml_Block_Dashboard_Grid
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
        $collection = Mage::getResourceModel('sales/order_item_collection');
        	$collection->getSelect()->columns(array('grand_total'=>'sum(base_row_total)','items'=>'sum(qty_ordered)'))
        	->group('order_id')
        	->join(array('order_table'=>$collection->getTable('sales/order_grid')),'order_id=entity_id',array('billing_name'))
        	->where('main_table.vendor_id=?',$vendorId)
        	->order('created_at DESC');
        $this->setCollection($collection);

        return Mage_Adminhtml_Block_Dashboard_Grid::_prepareCollection();
    }


	protected function _prepareColumns()
    {
        $this->addColumn('billing_name', array(
            'header'    => $this->__('Customer'),
            'sortable'  => false,
            'index'     => 'billing_name',
            'default'   => $this->__('Guest'),
        ));

        $this->addColumn('items', array(
            'header'    => $this->__('Item'),
            'align'     => 'left',
            'sortable'  => false,
            'index'     => 'items'
        ));

        $baseCurrencyCode = Mage::app()->getStore((int)$this->getParam('store'))->getBaseCurrencyCode();

        $this->addColumn('total', array(
            'header'    => $this->__('Total'),
            'align'     => 'right',
            'sortable'  => false,
            'type'      => 'currency',
            'currency_code'  => $baseCurrencyCode,
            'index'     => 'grand_total'
        ));

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/sales_order/view', array('order_id'=>$row->getOrderId()));
    }
}
