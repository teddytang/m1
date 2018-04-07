<?php

class VES_VendorsCredit_Block_Adminhtml_Payment_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('vendorsPaymentGrid');
      $this->setDefaultSort('sort_order');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('vendorscredit/payment')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('method_id', array(
          'header'    => Mage::helper('vendorscredit')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'method_id',
      ));
	  $this->addColumn('name', array(
          'header'    => Mage::helper('vendorscredit')->__('Name'),
          'align'     =>'left',
      	  'width'     => '170px',
          'index'     => 'name',
      ));
      $baseCurrencyCode = Mage::app()->getStore((int)$this->getParam('store'))->getBaseCurrencyCode();
      $this->addColumn('fee', array(
          'header'    => Mage::helper('vendorscredit')->__('Fee'),
          'align'     =>'left',
      	  'width'     => '100px',
      	  'type'      => 'currency',
          'currency_code'  => $baseCurrencyCode,
          'index'     => 'fee',
      ));	  
	  
      $this->addColumn('description', array(
			'header'    => Mage::helper('vendorscredit')->__('Description'),
			'index'     => 'description',
      ));
	  $this->addColumn('sort_order', array(
          'header'    => Mage::helper('vendorscredit')->__('Sort Order'),
      	  'width'     => '50px',
          'index'     => 'sort_order',
      ));
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('message_id');
        $this->getMassactionBlock()->setFormFieldName('vendors');
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}