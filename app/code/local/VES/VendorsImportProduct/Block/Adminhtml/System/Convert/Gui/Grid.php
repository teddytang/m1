<?php
class VES_VendorsImportProduct_Block_Adminhtml_System_Convert_Gui_Grid extends Mage_Adminhtml_Block_System_Convert_Gui_Grid
{
	protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('dataflow/profile_collection')
            ->addFieldToFilter('entity_type', array('notnull'=>''))
            ->addFieldToFilter('is_ves_marketplace',0);

        $this->setCollection($collection);

        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }
}