<?php

class VES_Vendors_Model_Source_Backendtype extends Varien_Object
{
    const BACKEND_TYPE_VARCHAR	= 'varchar';
	const BACKEND_TYPE_INT		= 'int';
    const BACKEND_TYPE_STATIC	= 'static';
    
	public function getAllOptions()
    {
    	return array(
			array(
            	'label' => Mage::helper('vendors')->__('Varchar'),
                'value' =>  self::BACKEND_TYPE_VARCHAR
			),
            array(
            	'label' => Mage::helper('vendors')->__('Int'),
                'value' =>  self::BACKEND_TYPE_INT
           	),
           	array(
            	'label' => Mage::helper('vendors')->__('Static'),
                'value' =>  self::BACKEND_TYPE_STATIC
           	),
		);
    }
}