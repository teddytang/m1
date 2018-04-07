<?php
class VES_VendorsCredit_Model_Type_Admin extends VES_VendorsCredit_Model_Type_Abstract
{
	public function process(){
		$customerCredit = $this->getCustomerCredit();
		$vendorId		= $this->getVendorId();
		$vendor 		= Mage::getModel('vendors/vendor')->load($vendorId);
    	$type			= $this->getType();
    	$amount			= $this->getAmount();
    	$note			= $this->getData('description');
    	$description	= $note?$note:Mage::helper('vendorssales')->__('You got credit from admin');
    	$action 		= $this->getAction();
    	/*Do nothing if the amount is zero*/
    	if(!$amount) return;
    	
    	$result = $this->processAmount($vendor, $action, $amount);
    	
    	/*Save transaction*/
    	$transaction = Mage::getModel('vendorscredit/transaction')->setData(array(
    		'vendor_id'			=> $vendorId,
    		'type'				=> $type,
    		'amount'			=> $amount,
    		'fee'				=> 0,
	    	'net_amount'		=> $amount,
	    	'balance'			=> $vendor->getCredit(),
	    	'description'		=> $description,
	    	'additional_info'	=> '',
	    	'created_at'		=> now(),
    	))->save();
	}
	
	public function getDescription(VES_VendorsCredit_Model_Transaction $transaction){
		return $transaction->getDescription();
	}
}