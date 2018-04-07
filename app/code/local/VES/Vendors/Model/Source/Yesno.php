<?php

class VES_Vendors_Model_Source_Yesno extends Varien_Object
{   
	public function getAllOptions()
    {
    	return array(
			array(
            	'label' => Mage::helper('vendors')->__('No'),
                'value' =>  0
			),
            array(
            	'label' => Mage::helper('vendors')->__('Yes'),
                'value' =>  1
           	),
		);
    }
}