<?php

/**
 * Vendor attribute add/edit form main tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class VES_Vendors_Block_Adminhtml_Vendors_Attribute_Edit_Tab_Main extends Mage_Eav_Block_Adminhtml_Attribute_Edit_Main_Abstract
{
    /**
     * Adding product form elements for editing attribute
     *
     * @return Mage_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tab_Main
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $attributeObject = $this->getAttributeObject();
        /* @var $form Varien_Data_Form */
        $form = $this->getForm();
        /* @var $fieldset Varien_Data_Form_Element_Fieldset */
        $fieldset = $form->getElement('base_fieldset');

        if($attributeObject->getId() && !$attributeObject->getData('is_user_defined')){
			$form->getElement('is_required')->setData('disabled','disabled');
			$form->getElement('frontend_class')->setData('disabled','disabled');
        }
        $fieldset->addField('default_value_extension', 'text', array(
            'name' => 'default_value_extension',
            'label' => Mage::helper('eav')->__('Allow file extensions'),
            'title' => Mage::helper('eav')->__('Allow file extensions'),
            'value' => $attributeObject->getDefaultValue(),
            'note' => 'Separate by comma<br />e.g. doc,docx,excel',
        ),'default_value_text');
        
        Mage::dispatchEvent('adminhtml_vendor_attribute_edit_prepare_form', array(
            'form'      => $form,
            'attribute' => $attributeObject
        ));

        $frontendInput = $form->getElement('frontend_input');
        $frontendInputValues = $frontendInput->getValues();
        $additionalInput = array(array('value'=>'file','label'=>Mage::helper('vendors')->__('File')));
        $frontendInputValues = array_merge($frontendInputValues,$additionalInput);
        $frontendInput->setValues($frontendInputValues);
        
        return Mage_Adminhtml_Block_Widget_Form::_prepareForm();
    }
}
