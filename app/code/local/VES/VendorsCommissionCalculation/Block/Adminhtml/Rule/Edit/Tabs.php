<?php

/**
 * description
 *
 * @category    Mage
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class VES_VendorsCommissionCalculation_Block_Adminhtml_Rule_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('vendors_commission_calculation_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('vendorscommission')->__('Commission Rule'));
    }
}
