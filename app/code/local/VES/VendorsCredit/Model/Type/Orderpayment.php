<?php
class VES_VendorsCredit_Model_Type_Orderpayment extends VES_VendorsCredit_Model_Type_Abstract
{
	public function process(){
		$vendor 		= $this->getVendor();
    	$type			= $this->getType();
    	$amount			= $this->getAmount();
    	$fee			= $this->getFee();
    	$netAmount		= $amount - $fee;
    	$order			= $this->getOrder();
    	$invoice		= $this->getInvoice();
    	$action 		= $this->getAction();
    	/*Do nothing if the amount is zero*/
    	if(!$amount) return;
    	
    	$result = $this->processAmount($vendor, $action, $netAmount);
    	    	
    	$description	= Mage::helper('vendorscredit')->__('Payment from order #%s, invoice #%s',$order->getIncrementId(),$invoice->getIncrementId());
    	//$additionalInfo	= serialize(array('order'=>$order->getId(),'invoice'=>$invoice->getId()));
    	$additionalInfo		= 'order|'.$order->getId().'||invoice|'.$invoice->getId();
    	
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
		
		$orderId 		= $addInfo['order'];
		$invoiceId 		= $addInfo['invoice'];
		$order 			= Mage::getModel('sales/order')->load($orderId);
		$invoice		= Mage::getModel('sales/order_invoice')->load($invoiceId);
		$description	= Mage::helper('vendorscredit')->__('Payment from order #%s, invoice #%s',$order->getIncrementId(),$invoice->getIncrementId());
		return $description;
	}
}