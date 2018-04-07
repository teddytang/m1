<?php
/**
 * description
 *
 * @category    VES
 * @package     VES_VendorsCommissionCalculation
 * @author      VnEcoms Core Team <support@vnecoms.com>
 */
class VES_VendorsCommissionCalculation_Block_Adminhtml_Rule_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('vendors_commission_form');
        $this->setTitle(Mage::helper('catalogrule')->__('Rule Information'));
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array('id' => 'edit_form', 'action' => $this->getUrl('*/*/save'), 'method' => 'post'));
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
