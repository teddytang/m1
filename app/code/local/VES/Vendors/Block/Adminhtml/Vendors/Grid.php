<?php

class VES_Vendors_Block_Adminhtml_Vendors_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('vendorsGrid');
      $this->setDefaultSort('entity_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('vendors/vendor')->getCollection()
      					//->addNameToSelect()
      					->addAttributeToSelect('telephone')
      					->addAttributeToSelect('postcode')
      					->addAttributeToSelect('country_id')
      					->addAttributeToSelect('group_id')
      					->addAttributeToSelect('firstname')
      					->addAttributeToSelect('lastname')
      					;
      //$collection->getSelect()->columns('CONCAT(firstname,lansname) as name');
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('entity_id', array(
          'header'    => Mage::helper('vendors')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'entity_id',
      ));
	  
      $this->addColumn('vendor_id', array(
          'header'    => Mage::helper('vendors')->__('Vendor'),
          'align'     =>'left',
      	  'width'     => '150px',
          'index'     => 'vendor_id',
      ));
      
      $this->addColumn('firstname', array(
          'header'    => Mage::helper('vendors')->__('First Name'),
          'align'     =>'left',
      	  'width'     => '150px',
          'index'     => 'firstname',
      ));

	  $this->addColumn('lastname', array(
          'header'    => Mage::helper('vendors')->__('Last Name'),
          'align'     =>'left',
      	  'width'     => '150px',
          'index'     => 'lastname',
      ));
	  
	  $this->addColumn('group_id', array(
          'header'    => Mage::helper('vendors')->__('Group'),
          'align'     =>'left',
      	  'width'     => '150px',
	  	  'type'	  => 'options',
	  	  'options'	  => Mage::getModel('vendors/source_group')->getOptionArray(),
          'index'     => 'group_id',
      ));
	  
      $this->addColumn('email', array(
			'header'    => Mage::helper('vendors')->__('Email'),
			'index'     => 'email',
      ));
      
	  $this->addColumn('telephone', array(
			'header'    => Mage::helper('vendors')->__('Telephone'),
			'index'     => 'telephone',
      ));
      
	  $this->addColumn('postcode', array(
			'header'    => Mage::helper('vendors')->__('Zip'),
			'index'     => 'postcode',
      ));
      $this->addColumn('country', array(
      		'width'		=> '100',
			'header'    => Mage::helper('vendors')->__('Country'),
      		'type'		=> 'country',
			'index'     => 'country_id',
      ));
      $this->addColumn('status', array(
          'header'    => Mage::helper('vendors')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => Mage::getModel('vendors/source_status')->getOptionArray(),
      ));
  	if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('website_id',
                array(
                    'header'=> Mage::helper('catalog')->__('Websites'),
                    'width' => '100px',
                    'sortable'  => false,
                    'index'     => 'website_id',
                    'type'      => 'options',
                    'options'   => Mage::getModel('core/website')->getCollection()->toOptionHash(),
            ));
        }
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('vendors')->__('Action'),
                'width'     => '50',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('vendors')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
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
        $this->setMassactionIdField('vendors_id');
        $this->getMassactionBlock()->setFormFieldName('vendors');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('vendors')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('vendors')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('vendors/source_status')->getAllOptions();

        //array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('vendors')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('vendors')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}