<?php

class VES_VendorsCredit_Block_Withdraw_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('vendorsTransactionGrid');
      $this->setDefaultSort('created_at');
      $this->setDefaultDir('DESC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
  	  $vendorId = Mage::getSingleton('vendors/session')->getVendorId();
      $collection = Mage::getModel('vendorscredit/withdrawal')->getCollection()->addFieldToFilter('vendor_id',$vendorId);
      $this->setCollection($collection);

      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
    	/*$this->addColumn('withdrawal_id', array(
            'header'    => $this->__('ID'),
    		'width'		=> '50px',
            'index'     => 'withdrawal_id',
        ));*/
  		$this->addColumn('created_at', array(
            'header'    => $this->__('Created At'),
        	'type'		=> 'datetime',
  			'width'		=> '150px',
            'index'     => 'created_at',
        ));
        $this->addColumn('updated_at', array(
            'header'    => $this->__('Updated At'),
        	'type'		=> 'datetime',
        	'width'		=> '150px',
            'index'     => 'updated_at',
        ));
    	$this->addColumn('method', array(
            'header'    => $this->__('Method'),
            'index'     => 'method',
        ));
        
		$baseCurrencyCode = Mage::app()->getStore((int)$this->getParam('store'))->getBaseCurrencyCode();

        $this->addColumn('amount', array(
            'header'    => $this->__('Amount'),
            'align'     => 'right',
            'type'      => 'currency',
        	'currency_code'  => $baseCurrencyCode,
            'index'     => 'amount'
        ));

        $this->addColumn('fee', array(
            'header'    => $this->__('Fee'),
            'align'     => 'right',
            'type'      => 'currency',
            'currency_code'  => $baseCurrencyCode,
            'index'     => 'fee',
        	'renderer'	=> new VES_VendorsCredit_Block_Widget_Grid_Column_Renderer_Minusamount(),
        ));

        $this->addColumn('net_amount', array(
            'header'    => $this->__('Net Amount'),
            'align'     => 'right',
            'type'      => 'currency',
            'currency_code'  => $baseCurrencyCode,
            'index'     => 'net_amount'
        ));
        $this->addColumn('status', array(
            'header'    => $this->__('Status'),
            'align'     => 'right',
        	'type'		=> 'options',
        	'width'		=> '100px',
        	'options'	=> Mage::getModel('vendorscredit/source_withdrawal_status')->getOptionArray(),
            'index'     => 'status',
        	'renderer'  => new VES_VendorsCredit_Block_Widget_Grid_Column_Renderer_Options(),
        ));
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/view', array('id' => $row->getId()));
  }

}