<?php

class VES_Vendors_Model_Resource_Eav_Attribute extends Mage_Eav_Model_Entity_Attribute
{
	public function _construct()
    {    
        $this->_init('vendors/attribute');
    }
}