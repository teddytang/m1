<?php
/**
 * Customer edit block
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsProduct_Block_Vendor_Product_Edit_Tab_Settings extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Settings
{
    protected function _prepareForm(){
    	parent::_prepareForm();
    	$form 		= $this->getForm();
    	$fieldSet 	= $form->getElement('settings');
    	$setField 	= $form->getElement('attribute_set_id');
    	if($setField) $setField->setData('values',Mage::getModel('vendorsproduct/source_attributeset')->getVendorOptionArray());
    	
    	$productTypeField 	= $form->getElement('product_type');
    	if($productTypeField) $productTypeField->setData('values',Mage::getModel('vendorsproduct/source_type')->getVendorOptionArray());
    }
}
