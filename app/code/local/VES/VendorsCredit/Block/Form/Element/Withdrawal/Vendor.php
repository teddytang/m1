<?php
class VES_VendorsCredit_Block_Form_Element_Withdrawal_Vendor extends Varien_Data_Form_Element_Select
{
	public function getElementHtml(){
		$options = $this->getOptions();
		$value = $this->getValue();
		return '<a target="_blank" href="'.Mage::helper('adminhtml')->getUrl('adminhtml/vendors/edit',array('id'=>$value)).'">'.(isset($options[$value])?$options[$value]:'').'</a>';
	}
}