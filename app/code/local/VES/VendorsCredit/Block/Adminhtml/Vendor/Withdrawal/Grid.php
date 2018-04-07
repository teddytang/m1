<?php

class VES_VendorsCredit_Block_Adminhtml_Vendor_Withdrawal_Grid extends VES_VendorsCredit_Block_Withdraw_Grid{
	protected function _prepareLayout(){
		parent::_prepareLayout();
	}
	
	protected function _prepareCollection()
	{
		$collection = Mage::getModel('vendorscredit/withdrawal')->getCollection();
		$vendorTbl 	= $collection->getTable('vendors/vendor');
		$collection->getSelect()->join(array('ves_table'=>$vendorTbl),'main_table.vendor_id = ves_table.entity_id',array('vendor_identifier'=>'vendor_id'));
		
		$status = $this->getRequest()->getParam('status');
		if($status == 'pending'){
			$collection->getSelect()->where('main_table.status='.VES_VendorsCredit_Model_Withdrawal::STATUS_PENDING);
		}
		$this->setCollection($collection);
	
		return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
	}
	
	protected function _prepareColumns(){
		$this->addColumnAfter('vendor_identifier', array(
            'header'    => $this->__('Vendor Id'),
  			'width'		=> '150px',
            'index'     => 'vendor_identifier',
			'renderer'	=> new VES_VendorsCredit_Block_Widget_Grid_Column_Renderer_Vendor(),
        ),'created_at');
		parent::_prepareColumns();
	}
}