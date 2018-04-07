<?php

class VES_VendorsCommissionCalculation_Block_Adminhtml_Rule_Edit_Tab_Actions
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Prepare content for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('vendorscommission')->__('Actions');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('vendorscommission')->__('Actions');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('current_commission_rule');

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('action_fieldset', array(
                'legend' => Mage::helper('vendorscommission')->__('Calculate Commission Using The Following Information')
            )
        );

        $fieldset->addField('commission_by', 'select', array(
            'label'     => Mage::helper('vendorscommission')->__('Commission By'),
            'onchange'  => 'hideShowCommissionAction()',
            'name'      => 'commission_by',
            'options'   => array(
                'by_fixed' => Mage::helper('vendorscommission')->__('Fixed Amount'),
                'by_percent' => Mage::helper('vendorscommission')->__('Percent Of Product Price'),
            ),
        ));
        
        $fieldset->addField('commission_action', 'select', array(
            'label'     => Mage::helper('vendorscommission')->__('Calculate Commission Based On'),
            'name'      => 'commission_action',
            'options'   => array(
                'by_price_incl_tax' => Mage::helper('vendorscommission')->__('Product Price (Incl. Tax)'),
                'by_price_excl_tax' => Mage::helper('vendorscommission')->__('Product Price (Excl. Tax)'),
                'by_price_after_discount_incl_tax' => Mage::helper('vendorscommission')->__('Product Price After Discount (Incl. Tax)'),
                'by_price_after_discount_excl_tax' => Mage::helper('vendorscommission')->__('Product Price After Discount (Excl. Tax)'),
            ),
        ));
        
        
        $fieldset->addField('commission_amount', 'text', array(
            'name'      => 'commission_amount',
            'required'  => true,
            'class'     => 'validate-not-negative-number',
            'label'     => Mage::helper('vendorscommission')->__('Commission'),
        ));

        $fieldset->addField('stop_rules_processing', 'select', array(
            'label'     => Mage::helper('vendorscommission')->__('Stop Further Rules Processing'),
            'title'     => Mage::helper('vendorscommission')->__('Stop Further Rules Processing'),
            'name'      => 'stop_rules_processing',
            'options'   => array(
                '1' => Mage::helper('vendorscommission')->__('Yes'),
                '0' => Mage::helper('vendorscommission')->__('No'),
            ),
        ));

        $form->setValues($model->getData());

        //$form->setUseContainer(true);

        if ($model->isReadonly()) {
            foreach ($fieldset->getElements() as $element) {
                $element->setReadonly(true, true);
            }
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
