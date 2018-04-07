<?php
class VES_VendorsCommissionCalculation_Block_Adminhtml_Rule extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_rule';
        $this->_blockGroup = 'vendorscommission';
        $this->_headerText = Mage::helper('vendorscommission')->__('Commission Rules Manager');
        $this->_addButtonLabel = Mage::helper('vendorscommission')->__('Add Rule');
        parent::__construct();
    }
}