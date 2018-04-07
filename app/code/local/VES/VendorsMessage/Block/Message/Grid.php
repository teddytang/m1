<?php

class VES_VendorsMessage_Block_Message_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('vendorsMessageGrid');
      $this->setDefaultSort('created_at');
      $this->setDefaultDir('DESC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('vendorsmessage/message')->getCollection();
      if($state = Mage::registry('message_state')){
      	$msgbox = Mage::registry('message_box');
      	$collection->addFieldToFilter('state',array('in'=>$state))/*->addFieldToFilter('parent_message_id',0)*/->addFieldToFilter('msgbox_id',$msgbox->getId());
      }
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      /*$this->addColumn('message_id', array(
          'header'    => Mage::helper('vendorsmessage')->__('Message ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'message_id',
      ));*/
	  $this->addColumn('created_at', array(
          'header'    => Mage::helper('vendorsmessage')->__('Received At'),
          'align'     =>'left',
      	  'width'     => '170px',
	  	  'type'	  => 'datetime',
          'index'     => 'created_at',
	  	  'renderer'  => new VES_VendorsMessage_Block_Widget_Grid_Column_Renderer_Datetime(),
      ));
      $this->addColumn('from', array(
          'header'    => Mage::helper('vendorsmessage')->__('From'),
          'align'     =>'left',
      	  'width'     => '200px',
          'index'     => 'from',
      	  'renderer'	=> new VES_VendorsMessage_Block_Widget_Grid_Column_Renderer_Text(),
      ));	  
	  
      $this->addColumn('subject', array(
			'header'    => Mage::helper('vendorsmessage')->__('Subject'),
			'index'     => 'subject',
      		'renderer'	=> new VES_VendorsMessage_Block_Widget_Grid_Column_Renderer_Text(),
      ));
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('vendorsmessage')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('vendorsmessage')->__('View'),
                        'url'       => array('base'=> '*/*/view'),
                        'field'     => 'message_id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
                'renderer'	=> new VES_VendorsMessage_Block_Widget_Grid_Column_Renderer_Action(),
        ));
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('message_id');
        $this->getMassactionBlock()->setFormFieldName('messages');
		$action = $this->getRequest()->getActionName();
        if($action == 'inbox'){
        	$this->getMassactionBlock()->addItem('mark_as_unread', array(
	             'label'    => Mage::helper('vendorsmessage')->__('Mark As Unread'),
	             'url'      => $this->getUrl('*/*/massMarkAsUnread'),
	        ));
	        $this->getMassactionBlock()->addItem('mark_as_read', array(
	             'label'    => Mage::helper('vendorsmessage')->__('Mark As Read'),
	             'url'      => $this->getUrl('*/*/massMarkAsRead'),
	        ));
        }elseif($action == 'trash'){
        	$this->getMassactionBlock()->addItem('restore', array(
	             'label'    => Mage::helper('vendorsmessage')->__('Restore'),
	             'url'      => $this->getUrl('*/*/massRestore'),
	        ));
        }
		
        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('vendorsmessage')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('vendorsmessage')->__('Are you sure?')
        ));
        return $this;
    }

  	public function getRowUrl($row)
  	{
		return $this->getUrl('*/*/view', array('message_id' => $row->getId(),'back'=>$this->getRequest()->getActionName()));
  	}

}