<?php
class VES_VendorsProduct_Model_Source_Approval extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
	const STATUS_NOT_SUBMITED	= 0;
	const STATUS_PENDING		= 1;
	const STATUS_APPROVED		= 2;
	const STATUS_UNAPPROVED		= 3;
	
	static public function getOptionArray()
    {
        return array(
        	self::STATUS_NOT_SUBMITED  	=> Mage::helper('vendorsproduct')->__('Not Submited'),
            self::STATUS_PENDING    	=> Mage::helper('vendorsproduct')->__('Pending'),
            self::STATUS_APPROVED    	=> Mage::helper('vendorsproduct')->__('Approved'),
            self::STATUS_UNAPPROVED		=> Mage::helper('vendorsproduct')->__('Not Approved')
        );
    }
    
	public function getAllOptions()
    {
    	return array(
    		array(
            	'label' => Mage::helper('vendorsproduct')->__('Not Submited'),
                'value' =>  self::STATUS_NOT_SUBMITED
			),
			array(
            	'label' => Mage::helper('vendorsproduct')->__('Pending'),
                'value' =>  self::STATUS_PENDING
			),
            array(
            	'label' => Mage::helper('vendorsproduct')->__('Approved'),
                'value' =>  self::STATUS_APPROVED
           	),
           	array(
            	'label' => Mage::helper('vendorsproduct')->__('Not Approved'),
                'value' =>  self::STATUS_UNAPPROVED
           	),
		);
    }
	
	public function getFlatColums()
	{
			return array($this->getAttribute()->getAttributeCode() => array(
				'type'      => 'tinyint',
				'unsigned'  => true,
				'is_null'   => true,
				'default'   => null,
				'extra'     => null
			));
	}
	
	public function getFlatUpdateSelect($store)
	{
		return Mage::getResourceSingleton('eav/entity_attribute')
			->getFlatUpdateSelect($this->getAttribute(), $store);
	}
}