<?php
class VES_VendorsCredit_Block_Form_Element_Withdrawal_Status extends Varien_Data_Form_Element_Select
{
	public function getElementHtml()
    {
        $options 	= $this->getOptions();
        $value 		= $this->getValue();
        $result 	= isset($options[$value])?$options[$value]:'';
    	switch($value){
        	case VES_VendorsCredit_Model_Withdrawal::STATUS_PENDING:
        		$result = '<span class="status_yellow">'.$result.'</span>';
        		break;
        	case VES_VendorsCredit_Model_Withdrawal::STATUS_COMPLETE:
        		$result = '<span class="status_green">'.$result.'</span>';
        		break;
        	case VES_VendorsCredit_Model_Withdrawal::STATUS_CANCELED:
        		$result = '<span class="status_gray">'.$result.'</span>';
        		break;
        	
        }
        return $result;
    }
}