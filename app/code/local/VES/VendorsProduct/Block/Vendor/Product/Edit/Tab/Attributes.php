<?php
/**
 * Customer edit block
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsProduct_Block_Vendor_Product_Edit_Tab_Attributes extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Attributes
{
	public function getNotAllowedAttributes(){
		return Mage::helper('vendorsproduct')->getRestrictionProductAttribute();
	}
	
    protected function _prepareForm(){
    	parent::_prepareForm();
    	
    	$form 		= $this->getForm();
    	$group 		= $this->getGroup();

    	$fieldset	= $form->getElement('group_fields' . $group->getId());
    	/*Remove not allowed attributes from edit product page of vendor panel*/
    	$attributeCodes = $this->getNotAllowedAttributes();
    	foreach($attributeCodes as $attrCode){
    		$fieldset->removeField($attrCode);
    	}
		
		$news_date_from = $form->getElement('news_from_date');
		if ($news_date_from) {
			$news_date_from->setClass('validate-date');
		}
		$news_date_to = $form->getElement('news_to_date');
		if ($news_date_to) {
			$news_date_to->setClass('validate-date');
		}
		$special_from_date = $form->getElement('special_from_date');
		if ($special_from_date) {
			$special_from_date->setClass('validate-date');
		}
		$special_to_date = $form->getElement('special_to_date');
		if ($special_to_date) {
			$special_to_date->setClass('validate-date');
		}
    	
    	$tierPrice = $form->getElement('tier_price');
       	if ($tierPrice) {
        	$tierPrice->setRenderer(
        		$this->getLayout()->createBlock('vendorsproduct/vendor_product_edit_tab_price_tier')
        	);
        }
    	Mage::dispatchEvent('ves_vendorsproduct_prepare_form',array('fieldset'=>$fieldset));
    }
}
