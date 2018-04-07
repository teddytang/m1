<?php
class VES_VendorsCredit_Block_Form_Element_Withdrawal_Currency extends Varien_Data_Form_Element_Label
{
	public function getEscapedValue($index=null)
    {
        $value = $this->getValue($index);

        if ($filter = $this->getValueFilter()) {
            $value = $filter->filter($value);
        }
        $value = Mage::helper('core')->currency($value,true,false);
        if($this->getNegative()) $value = '-'.$value;
        $value = '<span style="color: #996515;font-weight: bold;">'.$value.'</span>';
        return $value;
    }
}