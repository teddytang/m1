<?php

class VES_Vendors_Model_Source_Frontendinput extends Varien_Object
{
    const FRONTEND_INPUT_TEXT	= 'text';
	const FRONTEND_INPUT_SELECT	= 'select';
    
	public function getAllOptions()
    {
    	return array(
			array(
            	'label' => Mage::helper('vendors')->__('Text'),
                'value' =>  self::FRONTEND_INPUT_TEXT
			),
            array(
            	'label' => Mage::helper('vendors')->__('Select'),
                'value' =>  self::FRONTEND_INPUT_SELECT
           	),
		);
    }
}