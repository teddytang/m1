<?php

class VES_VendorsCredit_Block_Adminhtml_Vendor_Transaction_Escrow_Grid extends Mage_Adminhtml_Block_Widget_Grid{
	protected function _prepareLayout(){
	    $this->setDefaultSort('escrow_id');
	    $this->setDefaultDir('DESC');
		parent::_prepareLayout();
	}
	
	protected function _prepareCollection(){
        $collection = Mage::getModel('vendorscredit/escrow')->getCollection();
		$vendorTbl 	= $collection->getTable('vendors/vendor');
		$collection->getSelect()->join(array('ves_table'=>$vendorTbl),'main_table.vendor_id = ves_table.entity_id',array('vendor_identifier'=>'vendor_id'));
		if(Mage::helper('vendors')->isAdvancedMode()){
            $collection->getSelect()->join(array('invoice_tbl'=>$collection->getTable('sales/invoice')),'main_table.relation_id = invoice_tbl.entity_id',array('invoice_increment_id'=>'increment_id'));
		}
		$this->setCollection($collection);
		return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
	}
	protected function _prepareColumns()
	{
	    $this->addColumn('escrow_id', array(
	        'header'    => $this->__('ID'),
	        'width'		=> '50',
	        'index'     => 'escrow_id',
	    ));
	    $this->addColumn('created_at', array(
	        'header'    => $this->__('Created At'),
	        'type'		=> 'datetime',
	      		'width'		=> '160px',
	        'index'     => 'created_at',
	    ));
	    
	    $this->addColumn('vendor_identifier', array(
	        'header'    => $this->__('Vendor Id'),
	        'index'     => 'vendor_identifier',
	        'renderer'	=> new VES_VendorsCredit_Block_Widget_Grid_Column_Renderer_Vendor(),
	        'width'		=> '150px',
	    ),'created_at');
	    /*Show invoice increment id if the extension is using advanced or advanced x mode*/
	    if(Mage::helper('vendors')->isAdvancedMode()){
    	    $this->addColumn('invoice_increment_id', array(
    	        'header'    => $this->__('Invoice Id#'),
    	        'index'     => 'invoice_increment_id',
    	        'width'		=> '100px',
    	    ),'created_at');
	    }
	    $this->addColumn('description', array(
	        'header'    => $this->__('Description'),
	        'index'     => 'description',
	        'renderer'	=> new VES_VendorsCredit_Block_Adminhtml_Vendor_Transaction_Escrow_Grid_Description(),
	        'filter'	=> false,
	        'sortable'	=> false,
	    ));
	
	    $baseCurrencyCode = Mage::app()->getStore((int)$this->getParam('store'))->getBaseCurrencyCode();
	
	    $this->addColumn('amount', array(
	        'header'    => $this->__('Amount'),
	        'align'     => 'right',
	        'type'      => 'currency',
	        'currency_code'  => $baseCurrencyCode,
	        'index'     => 'amount'
	    ));

	    $this->addColumn('status', array(
	        'header'    => $this->__('Status'),
	        'width'		=> '100px',
	        'type'      => 'options',
	        'options'   => Mage::getModel('vendorscredit/escrow')->getStatusOptionsArray(),
	        'index'     => 'status'
	    ));
	    $this->addColumn('action',
	        array(
	            'header'    => Mage::helper('catalog')->__('View'),
	            'width'     => '50px',
	            'type'      => 'action',
	            'getter'     => 'getId',
	            'actions'   => array(
	                array(
	                    'caption' => Mage::helper('catalog')->__('View'),
	                    'url'     => array(
	                        'base'=>'*/*/view',
	                        'params'=>array('store'=>$this->getRequest()->getParam('store'))
	                    ),
	                    'field'   => 'id'
	                )
	            ),
	            'filter'    => false,
	            'sortable'  => false,
	            'index'     => 'stores',
	        ));
	    return parent::_prepareColumns();
	}
		
	public function getRowUrl($row)
	{
	    return $this->getUrl('*/*/view', array(
	        'id'=>$row->getId())
	    );
	}
	
	protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            $field = ( $column->getFilterIndex() ) ? $column->getFilterIndex() : $column->getIndex();
            if ($column->getFilterConditionCallback()) {
                call_user_func($column->getFilterConditionCallback(), $this->getCollection(), $column);
            } else {
                $cond = $column->getFilter()->getCondition();
                if ($field && isset($cond)) {
                	if($field == 'vendor_identifier'){
                		$this->getCollection()->addFieldToFilter('ves_table.vendor_id' , $cond);
                	}elseif($field == 'invoice_increment_id'){
                		$this->getCollection()->addFieldToFilter('invoice_tbl.increment_id' , $cond);
                	}elseif($field=='created_at'){
                		$this->getCollection()->addFieldToFilter('main_table.created_at' , $cond);
                	}elseif($field=='status'){
                		$this->getCollection()->addFieldToFilter('main_table.status' , $cond);
                	}else{
                		$this->getCollection()->addFieldToFilter($field , $cond);
                	}
                }
            }
        }
        return $this;
    }
}