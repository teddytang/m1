<?php

class VES_VendorsMessage_Model_Msgbox extends Mage_Core_Model_Abstract
{
	public function _construct()
    {
        parent::_construct();
        $this->_init('vendorsmessage/msgbox');
    }
}