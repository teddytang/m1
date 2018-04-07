<?php

class VES_VendorsCredit_Model_Source_Withdrawal_Status
{
 	static public function getOptionArray()
    {
        return array(
            VES_VendorsCredit_Model_Withdrawal::STATUS_PENDING    	=> Mage::helper('vendorscredit')->__('Pending'),
            VES_VendorsCredit_Model_Withdrawal::STATUS_COMPLETE    	=> Mage::helper('vendorscredit')->__('Complete'),
            VES_VendorsCredit_Model_Withdrawal::STATUS_CANCELED  	=> Mage::helper('vendorscredit')->__('Canceled')
        );
    }
    
	public function getAllOptions()
    {
    	return array(
			array(
            	'label' => Mage::helper('vendorscredit')->__('Pending'),
                'value' =>  VES_VendorsCredit_Model_Withdrawal::STATUS_PENDING
			),
            array(
            	'label' => Mage::helper('vendorscredit')->__('Complete'),
                'value' =>  VES_VendorsCredit_Model_Withdrawal::STATUS_COMPLETE
           	),
           	array(
            	'label' => Mage::helper('vendorscredit')->__('Canceled'),
                'value' =>  VES_VendorsCredit_Model_Withdrawal::STATUS_CANCELED
           	),
		);
    }
}