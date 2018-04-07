<?php

class VES_VendorsCredit_Block_Transaction_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('vendorsTransactionGrid');
      $this->setDefaultSort('transaction_id');
      $this->setDefaultDir('DESC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
  	  $vendorId = Mage::getSingleton('vendors/session')->getVendorId();
      $collection = Mage::getModel('vendorscredit/transaction')->getCollection()
        ->addFieldToFilter('vendor_id',$vendorId)
        ->addOrder('transaction_id','desc');
      
      $this->setCollection($collection);

      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('created_at', array(
            'header'    => $this->__('Created At'),
        	'type'		=> 'datetime',
      		'width'		=> '160px',
            'sortable'	=> false,
            'index'     => 'created_at',
        ));
    	$this->addColumn('description', array(
            'header'    => $this->__('Description'),
            'index'     => 'description',
    		'renderer'	=> new VES_VendorsCredit_Block_Widget_Grid_Column_Renderer_Description(),
    		'filter'	=> false,
    		'sortable'	=> false,
        ));
        
		$baseCurrencyCode = Mage::app()->getStore((int)$this->getParam('store'))->getBaseCurrencyCode();

        $this->addColumn('amount', array(
            'header'    => $this->__('Amount'),
            'align'     => 'right',
            'type'      => 'currency',
            'sortable'	=> false,
        	'currency_code'  => $baseCurrencyCode,
        	'renderer'	=> new VES_VendorsCredit_Block_Widget_Grid_Column_Renderer_Amount(),
            'index'     => 'amount'
        ));

        $this->addColumn('fee', array(
            'header'    => $this->__('Fee'),
            'align'     => 'right',
            'type'      => 'currency',
            'sortable'	=> false,
            'currency_code'  => $baseCurrencyCode,
        	'renderer'	=> new VES_VendorsCredit_Block_Widget_Grid_Column_Renderer_Minusamount(),
            'index'     => 'fee'
        ));

        $this->addColumn('net_amount', array(
            'header'    => $this->__('Net Amount'),
            'sortable'	=> false,
            'align'     => 'right',
            'type'      => 'currency',
            'currency_code'  => $baseCurrencyCode,
        	'renderer'	=> new VES_VendorsCredit_Block_Widget_Grid_Column_Renderer_Amount(),
            'index'     => 'net_amount'
        ));
        $this->addColumn('balance', array(
            'header'    => $this->__('Balance'),
            'sortable'	=> false,
            'align'     => 'right',
            'type'      => 'currency',
            'currency_code'  => $baseCurrencyCode,
            'index'     => 'balance'
        ));
        
        $this->addExportType('*/*/exportCsv', Mage::helper('vendorscredit')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('vendorscredit')->__('XML'));
        
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        return $this;
    }

//  public function getRowUrl($row)
//  {
//      return $this->getUrl('*/*/view', array('id' => $row->getId()));
//  }

}