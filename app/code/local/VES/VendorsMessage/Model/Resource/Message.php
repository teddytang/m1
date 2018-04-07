<?php

class VES_VendorsMessage_Model_Resource_Message extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
    {    
        $this->_init('vendorsmessage/message', 'message_id');
    }
}