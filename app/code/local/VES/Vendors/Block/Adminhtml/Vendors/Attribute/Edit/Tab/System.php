<?php
/**
 * Vendor attribute add/edit form system tab
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */

class VES_Vendors_Block_Adminhtml_Vendors_Attribute_Edit_Tab_System extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $model = Mage::registry('entity_attribute');

        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('vendors')->__('System Properties')));

        if ($model->getAttributeId()) {
            $fieldset->addField('attribute_id', 'hidden', array(
                'name' => 'attribute_id',
            ));
        }

        $yesno = array(
            array(
                'value' => 0,
                'label' => Mage::helper('vendors')->__('No')
            ),
            array(
                'value' => 1,
                'label' => Mage::helper('vendors')->__('Yes')
            ));

        /*$fieldset->addField('attribute_model', 'text', array(
            'name' => 'attribute_model',
            'label' => Mage::helper('vendors')->__('Attribute Model'),
            'title' => Mage::helper('vendors')->__('Attribute Model'),
        ));

        $fieldset->addField('backend_model', 'text', array(
            'name' => 'backend_model',
            'label' => Mage::helper('vendors')->__('Backend Model'),
            'title' => Mage::helper('vendors')->__('Backend Model'),
        ));*/

        $fieldset->addField('backend_type', 'select', array(
            'name' => 'backend_type',
            'label' => Mage::helper('vendors')->__('Data Type for Saving in Database'),
            'title' => Mage::helper('vendors')->__('Data Type for Saving in Database'),
            'options' => array(
                'text'      => Mage::helper('vendors')->__('Text'),
                'varchar'   => Mage::helper('vendors')->__('Varchar'),
                'static'    => Mage::helper('vendors')->__('Static'),
                'datetime'  => Mage::helper('vendors')->__('Datetime'),
                'decimal'   => Mage::helper('vendors')->__('Decimal'),
                'int'       => Mage::helper('vendors')->__('Integer'),
            ),
        ));

        /*$fieldset->addField('backend_table', 'text', array(
            'name' => 'backend_table',
            'label' => Mage::helper('vendors')->__('Backend Table'),
            'title' => Mage::helper('vendors')->__('Backend Table Title'),
        ));

        $fieldset->addField('frontend_model', 'text', array(
            'name' => 'frontend_model',
            'label' => Mage::helper('vendors')->__('Frontend Model'),
            'title' => Mage::helper('vendors')->__('Frontend Model'),
        ));*/

        /*$fieldset->addField('is_visible', 'select', array(
            'name' => 'is_visible',
            'label' => Mage::helper('vendors')->__('Visible'),
            'title' => Mage::helper('vendors')->__('Visible'),
            'values' => $yesno,
        ));*/

        /*$fieldset->addField('source_model', 'text', array(
            'name' => 'source_model',
            'label' => Mage::helper('vendors')->__('Source Model'),
            'title' => Mage::helper('vendors')->__('Source Model'),
        ));*/

        $fieldset->addField('is_global', 'select', array(
            'name'  => 'is_global',
            'label' => Mage::helper('vendors')->__('Globally Editable'),
            'title' => Mage::helper('vendors')->__('Globally Editable'),
            'values'=> $yesno,
        ));

        $form->setValues($model->getData());

        if ($model->getAttributeId()) {
            $form->getElement('backend_type')->setDisabled(1);
            if ($model->getIsGlobal()) {
                #$form->getElement('is_global')->setDisabled(1);
            }
        } else {
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }

}
