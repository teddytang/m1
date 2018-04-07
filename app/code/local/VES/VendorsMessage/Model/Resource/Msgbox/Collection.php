<?php
class VES_VendorsMessage_Model_Resource_Msgbox_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	protected function _construct()
    {
        $this->_init('vendorsmessage/msgbox');
    }
}