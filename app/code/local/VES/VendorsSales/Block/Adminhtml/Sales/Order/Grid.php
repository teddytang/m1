<?php

/**
 * Vendor sales orders block
 *
 * @category   	VES
 * @package    	VES_Vendors
 * @author    	Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsSales_Block_Adminhtml_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
{
	protected function _prepareCollection()
    {
    	if(!Mage::helper('vendors')->isAdvancedMode()) return parent::_prepareCollection();
    	
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $collection->getSelect()->joinLeft(array('vendor_table'=>$collection->getTable('vendors/vendor')),'vendor_table.entity_id = main_table.vendor_id',array('vendor'=>'vendor_id'));
        $this->setCollection($collection);
        
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }
    
    protected function _prepareColumns(){
    	if(!Mage::helper('vendors')->isAdvancedMode()) return parent::_prepareColumns();
     	$this->addColumnAfter('vendor',
            array(
                'header'=> Mage::helper('vendors')->__('Vendor Id'),
                'index' => 'vendor',
            	'renderer'	=> new VES_VendorsSales_Block_Widget_Grid_Column_Renderer_Text(),
        	),
        'real_order_id');
    	return parent::_prepareColumns();
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
                	if($field == 'status'){
                		$this->getCollection()->addFieldToFilter('main_table.status' , $cond);
                	}elseif($field=='vendor'){
                		$this->getCollection()->addFieldToFilter('vendor_table.vendor_id' , $cond);
                	}elseif($field=='increment_id'){
                		$this->getCollection()->addFieldToFilter('main_table.increment_id' , $cond);
                	}elseif($field=='store_id'){
                		$this->getCollection()->addFieldToFilter('main_table.store_id' , $cond);
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
