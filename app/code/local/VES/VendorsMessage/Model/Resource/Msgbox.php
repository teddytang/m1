<?php

class VES_VendorsMessage_Model_Resource_Msgbox extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
    {    
        $this->_init('vendorsmessage/msgbox', 'msgbox_id');
    }
}