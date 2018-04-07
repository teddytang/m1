<?php

class VES_Vendors_Block_Adminhtml_Vendors_Attribute_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('vendorsAttributeGrid');
      $this->setDefaultSort('attribute_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $vendorAttributeType = Mage::getResourceModel('eav/entity_type_collection')->addFieldToFilter('entity_type_code','ves_vendor')->getFirstItem();
      $collection = Mage::getResourceModel('eav/entity_attribute_collection')
      ->addFieldToFilter('entity_type_id',$vendorAttributeType->getId())
      /*->addFieldToFilter('is_user_defined',true)*/;
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
  	/*
      $this->addColumn('attribute_id', array(
          'header'    => Mage::helper('vendors')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'attribute_id',
      ));
      */
	  $this->addColumn('attribute_code', array(
          'header'    => Mage::helper('vendors')->__('Attribute Code'),
          'width'     => '200px',
          'index'     => 'attribute_code',
      ));
      $this->addColumn('frontend_label', array(
          'header'    => Mage::helper('vendors')->__('Label'),
          'index'     => 'frontend_label',
      ));
      $this->addColumn('backend_type', array(
          'header'    => Mage::helper('vendors')->__('Type'),
      	  'width'     => '100px',
          'index'     => 'backend_type',
      ));
      $this->addColumn('frontend_input', array(
          'header'    => Mage::helper('vendors')->__('Frontend Input'),
      	  'width'     => '100px',
          'index'     => 'frontend_input',
      ));
      $this->addColumn('is_required', array(
          'header'    => Mage::helper('vendors')->__('Is Required'),
      	  'type'	  => 'options',
      	  'options'	  => array(
      		0	=> Mage::helper('vendors')->__('No'),
      		1	=> Mage::helper('vendors')->__('Yes'),
      	  ),
      	  'width'     => '100px',
          'index'     => 'is_required',
      ));
      $this->addColumn('is_user_defined', array(
          'header'    => Mage::helper('vendors')->__('Is User Defined'),
      	  'type'	  => 'options',
      	  'options'	  => array(
      		0	=> Mage::helper('vendors')->__('No'),
      		1	=> Mage::helper('vendors')->__('Yes'),
      	  ),
      	  'width'     => '100px',
          'index'     => 'is_user_defined',
      ));
      $this->addColumn('is_unique', array(
          'header'    => Mage::helper('vendors')->__('Is Unique'),
      	  'type'	  => 'options',
      	  'options'	  => array(
      		0	=> Mage::helper('vendors')->__('No'),
      		1	=> Mage::helper('vendors')->__('Yes'),
      	  ),
      	  'width'     => '100px',
          'index'     => 'is_unique',
      ));
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('vendors')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('vendors')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'attribute_id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('vendors')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('vendors')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
//        $this->setMassactionIdField('attribute_id');
//        $this->getMassactionBlock()->setFormFieldName('attribute');
//
//        $this->getMassactionBlock()->addItem('delete', array(
//             'label'    => Mage::helper('vendors')->__('Delete'),
//             'url'      => $this->getUrl('*/*/massDelete'),
//             'confirm'  => Mage::helper('vendors')->__('Are you sure?')
//        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('attribute_id' => $row->getId()));
  }

}