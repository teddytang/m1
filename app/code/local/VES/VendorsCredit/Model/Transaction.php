<?php

class VES_VendorsCredit_Model_Transaction extends Mage_Core_Model_Abstract
{

	public function _construct()
    {
        parent::_construct();
        $this->_init('vendorscredit/transaction');
    }
    
	protected function _afterSave()
    {
    	/*Send Notification email to vendor*/
    	Mage::helper('vendorscredit')->sendCreditBalanceChangeNotificationEmail($this);
        return parent::_afterSave();
    }
}