<?php

class VES_Vendors_Block_Adminhtml_Vendors_Group_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('vendorsGroupGrid');
      $this->setDefaultSort('vendor_group_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('vendors/group')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('vendor_group_id', array(
          'header'    => Mage::helper('vendors')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'vendor_group_id',
      ));
	  
	  $this->addColumn('vendor_group_code', array(
          'header'    => Mage::helper('vendors')->__('Name'),
          'align'     =>'left',
          'index'     => 'vendor_group_code',
      ));
	  
      $this->addColumn('fee', array(
          'header'    => Mage::helper('vendors')->__('Fee'),
          'align'     => 'left',
          'width'     => '50px',
          'index'     => 'fee',
      ));
      $this->addColumn('fee_by', array(
          'header'    => Mage::helper('vendors')->__('Calculate Fee By'),
          'align'     => 'left',
          'width'     => '150px',
          'index'     => 'fee_by',
          'type'      => 'options',
          'options'   => Mage::getModel('vendors/source_feetype')->getOptionArray(),
      ));
	  
		$this->addExportType('*/*/exportCsv', Mage::helper('vendors')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('vendors')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('vendors_id');
        $this->getMassactionBlock()->setFormFieldName('vendors');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('vendors')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('vendors')->__('Are you sure?')
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}