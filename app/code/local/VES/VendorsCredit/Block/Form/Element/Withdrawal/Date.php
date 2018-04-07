<?php
class VES_VendorsCredit_Block_Form_Element_Withdrawal_Date extends Varien_Data_Form_Element_Label
{
	public function getEscapedValue($index=null)
    {
        $value = $this->getValue($index);

        if ($filter = $this->getValueFilter()) {
            $value = $filter->filter($value);
        }
        $value = Mage::getModel('core/date')->date('M d, Y h:s:i A',$value);
        return $this->_escape($value);
    }
}