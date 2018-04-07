<?php

class VES_Vendors_Model_Source_Mode extends Varien_Object
{

	
    static public function toOptionArray()
    {
        return array(
            VES_Vendors_Model_Vendor::MODE_GENERAL    	=> Mage::helper('vendors')->__('General'),
            VES_Vendors_Model_Vendor::MODE_ADVANCED    	=> Mage::helper('vendors')->__('Advanced'),
            VES_Vendors_Model_Vendor::MODE_ADVANCED_X  	=> Mage::helper('vendors')->__('Advanced X'),
        );
    }
    
	public function getAllOptions()
    {
    	return array(
			array(
            	'label' => Mage::helper('vendors')->__('General'),
                'value' => VES_Vendors_Model_Vendor::MODE_GENERAL
			),
            array(
            	'label' => Mage::helper('vendors')->__('Advanced'),
                'value' => VES_Vendors_Model_Vendor::MODE_ADVANCED
           	),
           	array(
            	'label' => Mage::helper('vendors')->__('Advanced X'),
                'value' => VES_Vendors_Model_Vendor::MODE_ADVANCED_X
           	),
		);
    }
}