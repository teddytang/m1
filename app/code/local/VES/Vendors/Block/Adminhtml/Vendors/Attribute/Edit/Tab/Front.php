<?php
/**
 * Vendor attribute add/edit form main tab
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */

class VES_Vendors_Block_Adminhtml_Vendors_Attribute_Edit_Tab_Front extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $model = Mage::registry('entity_attribute');

        $form = new Varien_Data_Form(array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post'));

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('vendors')->__('Frontend Properties')));

        $yesno = array(
            array(
                'value' => 0,
                'label' => Mage::helper('vendors')->__('No')
            ),
            array(
                'value' => 1,
                'label' => Mage::helper('vendors')->__('Yes')
            ));


        $fieldset->addField('is_searchable', 'select', array(
            'name' => 'is_searchable',
            'label' => Mage::helper('vendors')->__('Use in Quick Search'),
            'title' => Mage::helper('vendors')->__('Use in Quick Search'),
            'values' => $yesno,
        ));

        $fieldset->addField('is_visible_in_advanced_search', 'select', array(
            'name' => 'is_visible_in_advanced_search',
            'label' => Mage::helper('vendors')->__('Use in Advanced Search'),
            'title' => Mage::helper('vendors')->__('Use in Advanced Search'),
            'values' => $yesno,
        ));

        $fieldset->addField('is_comparable', 'select', array(
            'name' => 'is_comparable',
            'label' => Mage::helper('vendors')->__('Comparable on the Frontend'),
            'title' => Mage::helper('vendors')->__('Comparable on the Frontend'),
            'values' => $yesno,
        ));


        $fieldset->addField('is_filterable', 'select', array(
            'name' => 'is_filterable',
            'label' => Mage::helper('vendors')->__("Use in Layered Navigation<br/>(Can be used only with catalog input type 'Dropdown')"),
            'title' => Mage::helper('vendors')->__('Can be used only with catalog input type Dropdown'),
            'values' => array(
                array('value' => '0', 'label' => Mage::helper('vendors')->__('No')),
                array('value' => '1', 'label' => Mage::helper('vendors')->__('Filterable (with results)')),
                array('value' => '2', 'label' => Mage::helper('vendors')->__('Filterable (no results)')),
            ),
        ));

//        if ($model->getIsUserDefined() || !$model->getId()) {
            $fieldset->addField('is_visible_on_front', 'select', array(
                'name' => 'is_visible_on_front',
                'label' => Mage::helper('vendors')->__('Visible on Catalog Pages on Front-end'),
                'title' => Mage::helper('vendors')->__('Visible on Catalog Pages on Front-end'),
                'values' => $yesno,
            ));
//        }

        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }

}
