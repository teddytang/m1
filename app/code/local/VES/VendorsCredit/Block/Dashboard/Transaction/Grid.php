<?php

/**
 * Vendor dashboard recent orders grid
 *
 * @category   	VES
 * @package    	VES_Vendors
 * @author    	Vnecoms Team <support@vnecoms.com>
 */

class VES_VendorsCredit_Block_Dashboard_Transaction_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

	public function __construct()
    {
        parent::__construct();
        $this->setId('lastOrdersGrid');
        $this->setDefaultSort('created_at');
      	$this->setDefaultDir('DESC');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('vendorscredit/transaction')->getCollection()->addFieldToFilter('vendor_id',Mage::getSingleton('vendors/session')->getVendorId());
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepares page sizes for dashboard grid with las 5 orders
     *
     * @return void
     */
    protected function _preparePage()
    {
        $this->getCollection()->setPageSize(5);
        // Remove count of total orders $this->getCollection()->setCurPage($this->getParam($this->getVarNamePage(), $this->_defaultPage));
    }

    protected function _prepareColumns()
    {
        $this->addColumn('created_at', array(
            'header'    => $this->__('Created At'),
            'sortable'  => false,
        	'type'		=> 'datetime',
            'index'     => 'created_at',
        ));
    	$this->addColumn('description', array(
            'header'    => $this->__('Description'),
            'sortable'  => false,
            'index'     => 'description',
			'renderer'	=> new VES_VendorsCredit_Block_Widget_Grid_Column_Renderer_Description(),
        ));
        
		$baseCurrencyCode = Mage::app()->getStore((int)$this->getParam('store'))->getBaseCurrencyCode();
		/*
        $this->addColumn('amount', array(
            'header'    => $this->__('Amount'),
            'align'     => 'right',
            'type'      => 'currency',
        	'currency_code'  => $baseCurrencyCode,
            'sortable'  => false,
            'index'     => 'amount'
        ));

        $this->addColumn('fee', array(
            'header'    => $this->__('Fee'),
            'align'     => 'right',
            'sortable'  => false,
            'type'      => 'currency',
            'currency_code'  => $baseCurrencyCode,
            'index'     => 'fee'
        ));
        */
        $this->addColumn('net_amount', array(
            'header'    => $this->__('Amount'),
            'align'     => 'right',
            'sortable'  => false,
            'type'      => 'currency',
            'currency_code'  => $baseCurrencyCode,
            'index'     => 'net_amount'
        ));
        $this->addColumn('balance', array(
            'header'    => $this->__('Balance'),
            'align'     => 'right',
            'sortable'  => false,
            'type'      => 'currency',
            'currency_code'  => $baseCurrencyCode,
            'index'     => 'balance'
        ));

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);

        return parent::_prepareColumns();
    }

//    public function getRowUrl($row)
//    {
//        return $this->getUrl('*/credit_transaction/view', array('trans_id'=>$row->getId()));
//    }
}
