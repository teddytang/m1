<?php

class VES_VendorsImportProduct_Block_Adminhtml_System_Convert_Gui_Edit_Tab_History extends Mage_Adminhtml_Block_System_Convert_Profile_Edit_Tab_History
{
	protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('dataflow/profile_history_collection')
            ->addFieldToFilter('profile_id', Mage::registry('current_convert_profile')->getId())
            ->addFieldToFilter('action_code','run');
        $collection->getSelect()->joinLeft(array('vendor_table'=>$collection->getTable('vendors/vendor')),'vendor_table.entity_id = main_table.vendor_id',array('vendor'=>'vendor_id','email'));
        $this->setCollection($collection);
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }
    protected function _prepareColumns()
    {
    	$this->addColumn('performed_at', array(
            'header'    => Mage::helper('adminhtml')->__('Performed At'),
            'type'      => 'datetime',
            'index'     => 'performed_at',
            'width'     => '150px',
        ));
        
    	$this->addColumn('vendor', array(
            'header'    => Mage::helper('adminhtml')->__('Vendor'),
            'index'     => 'vendor',
        ));

        $this->addColumn('email', array(
            'header'    => Mage::helper('adminhtml')->__('Email'),
            'index'     => 'email',
        ));

    	
        
        return Mage_Adminhtml_Block_Widget_Grid::_prepareColumns();
    }
}
