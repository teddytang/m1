<?php

class VES_Vendors_Model_Source_Feetype extends Varien_Object
{
    static public function getOptionArray()
    {
        return array(
            VES_Vendors_Model_Group::CALCULATE_FEE_BY_FIXED_AMOUNT    					=> Mage::helper('vendors')->__('Fixed amount'),
            VES_Vendors_Model_Group::CALCULATE_FEE_BY_PERCENT_AMOUNT    				=> Mage::helper('vendors')->__('Percent of Order Total'),
            /*
            VES_Vendors_Model_Group::CALCULATE_FEE_BY_PERCENT_SUBTOTAL_AMOUNT    		=> Mage::helper('vendors')->__('Percent of Order Subtotal'),
            VES_Vendors_Model_Group::CALCULATE_FEE_BY_PERCENT_SUBTOTAL_AFTER_DISCOUNT   => Mage::helper('vendors')->__('Percent of Order Subtotal After Discount'),
            VES_Vendors_Model_Group::CALCULATE_FEE_BY_ITEM_ROWTOTAL_AMOUNT    			=> Mage::helper('vendors')->__('Percent of Item Row Total'),
            */
        );
    }
    
	public function getAllOptions()
    {
    	return array(
			array(
            	'label' => Mage::helper('vendors')->__('Fixed amount'),
                'value' => VES_Vendors_Model_Group::CALCULATE_FEE_BY_FIXED_AMOUNT
			),
            array(
            	'label' => Mage::helper('vendors')->__('Percent of Order Total'),
                'value' => VES_Vendors_Model_Group::CALCULATE_FEE_BY_PERCENT_AMOUNT
           	),
           	/*
           	array(
            	'label' => Mage::helper('vendors')->__('Percent of Order Subtotal'),
                'value' => VES_Vendors_Model_Group::CALCULATE_FEE_BY_PERCENT_SUBTOTAL_AMOUNT
           	),
           	array(
            	'label' => Mage::helper('vendors')->__('Percent of Order Subtotal After Discount'),
                'value' => VES_Vendors_Model_Group::CALCULATE_FEE_BY_PERCENT_SUBTOTAL_AFTER_DISCOUNT
           	),
           	array(
            	'label' => Mage::helper('vendors')->__('Percent of Item Row Total'),
                'value' => VES_Vendors_Model_Group::CALCULATE_FEE_BY_ITEM_ROWTOTAL_AMOUNT
           	),
           	*/
		);
    }
}