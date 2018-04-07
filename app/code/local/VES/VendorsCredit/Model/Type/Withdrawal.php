<?php
class VES_VendorsCredit_Model_Type_Withdrawal extends VES_VendorsCredit_Model_Type_Abstract
{
	public function process(){
		$vendor 		= $this->getVendor();
    	$type			= $this->getType();
    	$amount			= $this->getAmount();
    	$fee			= 0;
    	$netAmount		= $amount;
    	$withdrawal		= $this->getWithdrawal();
    	$action 		= $this->getAction();
		
    	/*Do nothing if the amount is zero*/
    	if(!$amount) return;
    	
    	$this->processAmount($vendor, $action, $netAmount);
    	
    	$description	= Mage::helper('vendorscredit')->__('Withdraw money using "%s"',$withdrawal->getMethod());
    	//$additionalInfo	= serialize(array('withdrawal_id'=>$withdrawal->getId(),'note'=>$withdrawal->getNote()));
    	$additionalInfo	= 'withdrawal_id|'.$withdrawal->getId().'||note|'.$withdrawal->getNote();
    	
    	/*Save transaction*/
    	$transaction = Mage::getModel('vendorscredit/transaction')->setData(array(
    		'vendor_id'			=> $vendor->getId(),
    		'type'				=> $type,
    		'amount'			=> $amount,
    		'fee'				=> $fee,
	    	'net_amount'		=> $netAmount,
	    	'balance'			=> $vendor->getCredit(),
	    	'description'		=> $description,
	    	'additional_info'	=> $additionalInfo,
	    	'created_at'		=> now(),
    	))->save();
	}
	
	public function getDescription(VES_VendorsCredit_Model_Transaction $transaction){
		$additionalInfo = explode('||',$transaction->getAdditionalInfo());
		$addInfo = array();
		foreach($additionalInfo as $data){
			$tmpData = explode('|',$data);
			if(sizeof($tmpData) == 2){
				$addInfo[$tmpData[0]] = $tmpData[1];
			}
		}
		$withdrawalId	= $addInfo['withdrawal_id'];
		$withdrawal 	= Mage::getModel('vendorscredit/withdrawal')->load($withdrawalId);
		if($withdrawal->getId()){
			if(isset($addInfo['note']) && $addInfo['note']){
				$description	= Mage::helper('vendorscredit')->__('Withdraw money using "%s", "%s"',$withdrawal->getMethod(),$addInfo['note']);	
			}else{
				$description	= Mage::helper('vendorscredit')->__('Withdraw money using "%s"',$withdrawal->getMethod());
			}
		}else{
			$description	= $transaction->getDescription();
		}
		return $description;
	}
}