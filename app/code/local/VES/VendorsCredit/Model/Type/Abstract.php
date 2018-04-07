<?php
class VES_VendorsCredit_Model_Type_Abstract extends Varien_Object
{
	public function process(){
		return $this;
	}
	
	public function processAmount($vendor, $action, $netAmount){
		switch ($action){
			case 'add':
				$vendor->setCredit($vendor->getCredit()+$netAmount)->save();
				break;
			case 'subtract':
				if($vendor->getCredit() < $netAmount){throw new Mage_Core_Exception('You do not have enough credit amount to do this action');}
				$vendor->setCredit($vendor->getCredit()-$netAmount)->save();
				break;
		}
	}
	/**
	 * 
	 * Get transaction description
	 * @param VES_VendorsCredit_Model_Transaction $transaction
	 */
	public function getDescription(VES_VendorsCredit_Model_Transaction $transaction){
		return $this;
	}
}