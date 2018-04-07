<?php

/**
 * Edit vendor product attribute set
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsProduct_Block_Adminhtml_Catalog_Product_Attribute_Set_Main_Formset extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepares attribute set form
     *
     */
    protected function _prepareForm()
    {
        $data = Mage::getModel('eav/entity_attribute_set')
        ->load($this->getRequest()->getParam('id'));
    
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('set_name', array('legend'=> Mage::helper('catalog')->__('Edit Set Name')));
        $fieldset->addField('attribute_set_name', 'label', array(
            'label'     => Mage::helper('catalog')->__('Name'),
            'note'      => Mage::helper('catalog')->__('<a href="%s" target="_blank">Click here</a> to edit product attribute set',$this->getUrl('*/catalog_product_set/edit',array('id'=>$data->getId()))),
            'name'      => 'attribute_set_name',
            'required'  => true,
            'class'     => 'required-entry validate-no-html-tags',
            'value'     => $data->getAttributeSetName()
        ));
        
        $vendorSet = Mage::registry('vendor_attribute_set');
        $fieldset1 = $form->addFieldset('set_configuration', array('legend'=> Mage::helper('catalog')->__('Configuration')));
        $fieldset1->addField('group_display_type', 'select', array(
            'label'     => Mage::helper('catalog')->__('Display Groups As'),
            'name'      => 'group_display_type',
            'required'  => true,
            'options'   => array(
                VES_VendorsProduct_Model_Catalog_Product_Attribute_Set::DISPLAY_TYPE_FIELDSETS =>Mage::helper('vendorsproduct')->__('Fieldsets'),
                VES_VendorsProduct_Model_Catalog_Product_Attribute_Set::DISPLAY_TYPE_TABS      =>Mage::helper('vendorsproduct')->__('Tabs'),
            ),
            'value'     => $vendorSet->getData('group_display_type'),
        ));
    
        $form->setMethod('post');
        $form->setUseContainer(true);
        $form->setId('set_prop_form');
        $form->setAction($this->getUrl('*/*/save'));
        $form->setOnsubmit('return false;');
        $this->setForm($form);
    }
}
