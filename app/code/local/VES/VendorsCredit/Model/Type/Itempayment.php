<?php
class VES_VendorsCredit_Model_Type_Itempayment extends VES_VendorsCredit_Model_Type_Abstract
{
	public function process(){
		$vendor 		= $this->getVendor();
    	$type			= $this->getType();
    	$amount			= $this->getAmount();
    	$fee			= $this->getFee();
    	$netAmount		= $amount - $fee;
    	$invoiceItem	= $this->getItem();
    	$orderItem 		= $invoiceItem->getOrderItem();
    	$order			= $this->getOrder();
    	$invoice		= $this->getInvoice();
    	$action 		= $this->getAction();
    	/*Do nothing if the amount is zero*/
    	if(!$amount) return;
    	
    	$result = $this->processAmount($vendor, $action, $netAmount);
    	    	
    	$description		= Mage::helper('vendorscredit')->__('Payment from  order #%s, item %s x %s',$order->getIncrementId(),$invoiceItem->getName(),$invoiceItem->getQty()*1);
    	$description .= $this->getAdditionalDescription();
    	$additionalInfo		= 'order_item|'.$orderItem->getId().'||order|'.$order->getId().'||invoice|'.$invoice->getId();
    	
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
		$orderItemId	= $addInfo['order_item'];
		
		$order 			= Mage::getModel('sales/order')->load($orderId);
		//$invoice		= Mage::getModel('sales/order_invoice')->load($invoiceId);
		$orderItem		= $order->getItemById($orderItemId);
		$description	= Mage::helper('vendorscredit')->__('Payment from  order #%s, item %s x %s',$order->getIncrementId(),$orderItem->getName(),$orderItem->getQtyOrdered()*1);
		return $description;
	}
}