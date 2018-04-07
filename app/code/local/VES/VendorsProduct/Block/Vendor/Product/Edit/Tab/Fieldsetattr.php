<?php
/**
 * Customer edit block
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsProduct_Block_Vendor_Product_Edit_Tab_Fieldsetattr extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Attributes
{
    public function getNotAllowedAttributes(){
        return Mage::helper('vendorsproduct')->getRestrictionProductAttribute();
    }
    /**
     * Prepare attributes form
     *
     * @return null
     */
    protected function _prepareForm()
    {
        $groupCollection = $this->getGroupCollection();
        $form = new Varien_Data_Form();
        // Initialize product object as form property to use it during elements generation
        $form->setDataObject(Mage::registry('product'));
        
        foreach ($groupCollection as $group) {
            // do not add groups without attributes
        
            $entityAttributeCollection = Mage::getResourceModel('vendorsproduct/catalog_product_attribute_collection')
            ->setAttributeGroupFilter($group->getId())
            ->addVisibleFilter()
            ->checkConfigurableProducts()
            ->load();
        
            if ($entityAttributeCollection->count()==0) {
                continue;
            }

            
    
            $fieldset = $form->addFieldset('group_fields' . $group->getId(), array(
                'legend' => Mage::helper('catalog')->__($group->getAttributeGroupName()),
                'class' => 'fieldset-wide'
            ));
    
    
            $this->_setFieldset($entityAttributeCollection, $fieldset, array('gallery'));
    
            /*Remove not allowed attributes from edit product page of vendor panel*/
            $attributeCodes = $this->getNotAllowedAttributes();
            if($attributeCodes && is_array($attributeCodes)) foreach($attributeCodes as $attrCode){
                $fieldset->removeField($attrCode);
            }
            
            $urlKey = $form->getElement('url_key');
            if ($urlKey) {
                $urlKey->setRenderer(
                    $this->getLayout()->createBlock('adminhtml/catalog_form_renderer_attribute_urlkey')
                );
            }
    
            $tierPrice = $form->getElement('tier_price');
            if ($tierPrice) {
                $tierPrice->setRenderer(
                    $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_price_tier')
                );
            }
    
            $groupPrice = $form->getElement('group_price');
            if ($groupPrice) {
                $groupPrice->setRenderer(
                    $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_price_group')
                );
            }
    
            $recurringProfile = $form->getElement('recurring_profile');
            if ($recurringProfile) {
                $recurringProfile->setRenderer(
                    $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_price_recurring')
                );
            }
    
            // Add new attribute button if it is not an image tab
            if (!$form->getElement('media_gallery')
                && Mage::getSingleton('admin/session')->isAllowed('catalog/attributes/attributes')
            ) {
                $headerBar = $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_attributes_create');
    
                $headerBar->getConfig()
                ->setTabId('group_' . $group->getId())
                ->setGroupId($group->getId())
                ->setStoreId($form->getDataObject()->getStoreId())
                ->setAttributeSetId($form->getDataObject()->getAttributeSetId())
                ->setTypeId($form->getDataObject()->getTypeId())
                ->setProductId($form->getDataObject()->getId());
    
                $fieldset->setHeaderBar($headerBar->toHtml());
            }
    
            if ($form->getElement('meta_description')) {
                $form->getElement('meta_description')->setOnkeyup('checkMaxLength(this, 255);');
            }
    
            $values = Mage::registry('product')->getData();
    
            // Set default attribute values for new product
            if (!Mage::registry('product')->getId()) {
                foreach ($entityAttributeCollection as $attribute) {
                    if (!isset($values[$attribute->getAttributeCode()])) {
                        $values[$attribute->getAttributeCode()] = $attribute->getDefaultValue();
                    }
                }
            }
    
            if (Mage::registry('product')->hasLockedAttributes()) {
                foreach (Mage::registry('product')->getLockedAttributes() as $attribute) {
                    $element = $form->getElement($attribute);
                    if ($element) {
                        $element->setReadonly(true, true);
                    }
                }
            }
            $form->addValues($values);
            $form->setFieldNameSuffix('product');
    
            Mage::dispatchEvent('adminhtml_catalog_product_edit_prepare_form', array('form' => $form));
    
            $this->setForm($form);
        }
    }
}
