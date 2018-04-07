<?php

class VES_VendorsCredit_Block_Adminhtml_Vendor_Transaction_Grid extends VES_VendorsCredit_Block_Transaction_Grid{
	protected function _prepareLayout(){
		parent::_prepareLayout();
	}
	
	protected function _prepareCollection(){
		$collection = Mage::getModel('vendorscredit/transaction')->getCollection();
		$vendorTbl 	= $collection->getTable('vendors/vendor');
		$collection->getSelect()
		  ->join(array('ves_table'=>$vendorTbl),'main_table.vendor_id = ves_table.entity_id',array('vendor_identifier'=>'vendor_id'))
		  ->order('transaction_id DESC');

		$this->setCollection($collection);
		return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
	}
	
	protected function _prepareColumns(){
		parent::_prepareColumns();
		$this->addColumnAfter('vendor_identifier', array(
            'header'    => $this->__('Vendor Id'),
            'index'     => 'vendor_identifier',
			'renderer'	=> new VES_VendorsCredit_Block_Widget_Grid_Column_Renderer_Vendor(),
			'width'		=> '150px',
        ),'created_at');
		
        return Mage_Adminhtml_Block_Widget_Grid::_prepareColumns();
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
                	}elseif($field=='created_at'){
                		$this->getCollection()->addFieldToFilter('main_table.created_at' , $cond);
                	}else{
                		$this->getCollection()->addFieldToFilter($field , $cond);
                	}
                }
            }
        }
        return $this;
    }
}