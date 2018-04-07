<?php
class VES_VendorsCredit_Model_Type_Default extends Varien_Object
{
	public function process(){
		$vendor 		= $this->getVendor();
    	$type			= $this->getType();
    	$amount			= $this->getAmount();
    	$fee			= $this->getFee();
    	$netAmount		= $amount - $fee;
    	$action 		= $this->getAction();
    	
       	$description	= Mage::helper('vendorssales')->__('You got credit');
       	
    	$result = $this->processAmount($vendor, $action, $netAmount);
       	
       	/*Save transaction*/
    	$transaction = Mage::getModel('vendorscredit/transaction')->setData(array(
    		'vendor_id'			=> $vendor->getId(),
    		'type'				=> $type,
    		'amount'			=> $amount,
    		'fee'				=> $fee,
	    	'net_amount'		=> $netAmount,
	    	'balance'			=> $vendor->getCredit(),
	    	'description'		=> $description,
	    	'additional_info'	=> '',
	    	'created_at'		=> now(),
    	))->save();
	}
	
	public function getDescription(VES_VendorsCredit_Model_Transaction $transaction){
		return Mage::helper('vendorssales')->__('You got credit');
	}
}