<?php

class VES_VendorsCredit_Helper_Data extends Mage_Core_Helper_Abstract
{
	const XML_PATH_CREDIT_BALANCE_CHANGE 		= 'vendors/credit/balance_change_template';
	const XML_PATH_CREDIT_NEW_WITHDRAWAL 		= 'vendors/credit/new_withdrawal_template';
    const XML_PATH_CREDIT_WITHDRAWAL_SUCCESS	= 'vendors/credit/withdrawal_success_template';
    const XML_PATH_CREDIT_WITHDRAWAL_REJECTED 	= 'vendors/credit/withdrawal_rejected_template';
    const XML_PATH_ESCROW_ACCOUNT_CHANGE 		= 'vendors/credit/escrow_template';
    const XML_PATH_ESCROW_RELEASE               = 'vendors/credit/escrow_release_template';
    const XML_PATH_ESCROW_CANCEL                = 'vendors/credit/escrow_cancel_template';
    
    const XML_PATH_CREDIT_EMAIL_IDENTITY		= 'vendors/credit/email_identity';
    
    /**
     * Add currency to price/amount
     * @param int $amount
     * @return string
     */
	public function formatCredit($amount){
		return Mage::helper('core')->currency($amount);
	}
	
	/**
	 * Get transaction description
	 * @param VES_VendorsCredit_Model_Transaction $transaction
	 */
	public function getTransactionDescription(VES_VendorsCredit_Model_Transaction $transaction){
		return Mage::getModel('vendorscredit/type')->getDescription($transaction);
	}
	/**
	 * Is enable the escrow account
	 * @return boolean
	 */
	public function isEnableEscrowAccount(){
	    return Mage::getStoreConfig('vendors/credit/enable_escrow');
	}
	
	/**
	 * 
	 * Send new withdrawal notification email to vendor
	 * @param VES_VendorsCredit_Model_Withdrawal $withdrawal
	 */
	public function sendNewWithdrawalNotificationEmail(VES_VendorsCredit_Model_Withdrawal $withdrawal){
        $vendor = Mage::getModel('vendors/vendor')->load($withdrawal->getVendorId());
        if(!$vendor->getId()) return;
        $withdrawal->setData('requested_on',Mage::getModel('core/date')->date('M d, Y h:s:i A',$withdrawal->getCreatedAt()));
        $withdrawal->setData('amount',$this->formatCredit($withdrawal->getAmount()));
        $withdrawal->setData('fee',$this->formatCredit($withdrawal->getFee()));
        $withdrawal->setData('net_amount',$this->formatCredit($withdrawal->getNetAmount()));
        $this->_sendEmailTemplate(self::XML_PATH_CREDIT_NEW_WITHDRAWAL, self::XML_PATH_CREDIT_EMAIL_IDENTITY,
            array('vendor' => $vendor, 'withdrawal' => $withdrawal),null,$vendor);
        return $this;
	}
	
	/**
	 * 
	 * Send success withdrawal notification email to vendor
	 * @param VES_VendorsCredit_Model_Withdrawal $withdrawal
	 */
	public function sendSuccessWithdrawalNotificationEmail(VES_VendorsCredit_Model_Withdrawal $withdrawal){
        $vendor = Mage::getModel('vendors/vendor')->load($withdrawal->getVendorId());
        if(!$vendor->getId()) return;
        $withdrawal->setData('requested_on',Mage::getModel('core/date')->date('M d, Y h:s:i A',$withdrawal->getCreatedAt()));
        $withdrawal->setData('amount',$this->formatCredit($withdrawal->getAmount()));
        $withdrawal->setData('fee',$this->formatCredit($withdrawal->getFee()));
        $withdrawal->setData('net_amount',$this->formatCredit($withdrawal->getNetAmount()));
        $this->_sendEmailTemplate(self::XML_PATH_CREDIT_WITHDRAWAL_SUCCESS, self::XML_PATH_CREDIT_EMAIL_IDENTITY,
            array('vendor' => $vendor, 'withdrawal' => $withdrawal),null,$vendor);
        return $this;
	}
	
	/**
	 * 
	 * Send success withdrawal notification email to vendor
	 * @param VES_VendorsCredit_Model_Withdrawal $withdrawal
	 */
	public function sendRejectedWithdrawalNotificationEmail(VES_VendorsCredit_Model_Withdrawal $withdrawal){
        $vendor = Mage::getModel('vendors/vendor')->load($withdrawal->getVendorId());
        if(!$vendor->getId()) return;
        $withdrawal->setData('requested_on',Mage::getModel('core/date')->date('M d, Y h:s:i A',$withdrawal->getCreatedAt()));
        $withdrawal->setData('amount',$this->formatCredit($withdrawal->getAmount()));
        $withdrawal->setData('fee',$this->formatCredit($withdrawal->getFee()));
        $withdrawal->setData('net_amount',$this->formatCredit($withdrawal->getNetAmount()));
        $this->_sendEmailTemplate(self::XML_PATH_CREDIT_WITHDRAWAL_REJECTED, self::XML_PATH_CREDIT_EMAIL_IDENTITY,
            array('vendor' => $vendor, 'withdrawal' => $withdrawal),null,$vendor);
        return $this;
	}
	/**
	 * Send credit balance change notification email to vendor
	 * @param VES_VendorsCredit_Model_Transaction $transaction
	 */
	public function sendCreditBalanceChangeNotificationEmail(VES_VendorsCredit_Model_Transaction $transaction){
		$vendor = Mage::getModel('vendors/vendor')->load($transaction->getVendorId());
        if(!$vendor->getId()) return;
        
        $type = Mage::getModel('vendorscredit/type')->getType($transaction->getType());
        $sign = $type['action']=='add'?'+':'-';
        
        $transaction->setData('amount',$sign.$this->formatCredit($transaction->getAmount()));
        $transaction->setData('fee','-'.$this->formatCredit($transaction->getFee()));
        $transaction->setData('net_amount',$sign.$this->formatCredit($transaction->getNetAmount()));
        $transaction->setData('balance',$this->formatCredit($transaction->getBalance()));
        $transaction->setData('created_at',Mage::getModel('core/date')->date('M d, Y h:s:i A',$transaction->getCreatedAt()));
        $this->_sendEmailTemplate(self::XML_PATH_CREDIT_BALANCE_CHANGE, self::XML_PATH_CREDIT_EMAIL_IDENTITY,
            array('vendor' => $vendor, 'transaction' => $transaction),null,$vendor);
	}

	
	public function sendEscrowNotificationEmail(VES_VendorsCredit_Model_Escrow $escrow){
        Mage::log('sendEscrowNotificationEmail');
	    $vendor = $escrow->getVendor();
	    if(!$vendor->getId()) return;
        if(!$escrow->getId()) return;

	    /*Send notification emails here*/

        switch($escrow->getStatus()) {
            case VES_VendorsCredit_Model_Escrow::STATUS_PENDING:$template = self::XML_PATH_ESCROW_ACCOUNT_CHANGE;break;
            case VES_VendorsCredit_Model_Escrow::STATUS_COMPLETED:$template = self::XML_PATH_ESCROW_RELEASE;break;
            case VES_VendorsCredit_Model_Escrow::STATUS_CANCELED:$template = self::XML_PATH_ESCROW_CANCEL;break;
        }

        //get order invoice info
        $addition_info = $escrow->getAdditionalInfo();
        $main_info = explode('||',$addition_info);
        $order_id = explode('|',$main_info['0']);$order_id = $order_id['1'];
        $invoice_id = explode('|',$main_info['1']);$invoice_id = $invoice_id['1'];
        $orderData = Mage::getModel('sales/order')->load($order_id);
        $invoiceData = Mage::getModel('sales/order_invoice')->load($invoice_id);

        //get products
        $orderItems = $orderData->getAllItems();
        $products = array();
        foreach($orderItems as $item) {
            $product = $item->getProductId();
            $products[] = Mage::getModel('catalog/product')->load($product);
        }
        //Mage::log($order_id);
        //Mage::log($orderData->getData());
        //Mage::log($invoiceData->getData());

	    $escrow->setData('amount',$this->formatCredit($escrow->getAmount()));
	    $escrow->setData('created_at',Mage::getModel('core/date')->date('M d, Y h:s:i A',$escrow->getCreatedAt()));
	    $escrow->setData('description', 'order #'.$orderData->getIncrementId().' - invoice #'.$invoiceData->getIncrementId());
        $this->_sendEmailTemplate($template, self::XML_PATH_CREDIT_EMAIL_IDENTITY,
	        array('vendor' => $vendor, 'escrow' => $escrow, 'order'=>$orderData, 'invoice'=>$invoiceData, 'products'=>$products),null,$vendor);

	}
	/**
     * Send corresponding email template
     *
     * @param string $emailTemplate configuration path of email template
     * @param string $emailSender configuration path of email identity
     * @param array $templateParams
     * @param int|null $storeId
     * @return VES_Vendors_Model_Vendor
     */
    protected function _sendEmailTemplate($template, $sender, $templateParams = array(), $storeId = null,$vendor)
    {
        /** @var $mailer Mage_Core_Model_Email_Template_Mailer */
        $mailer = Mage::getModel('core/email_template_mailer');
        $emailInfo = Mage::getModel('core/email_info');
        $emailInfo->addTo($vendor->getEmail(), $vendor->getName());
        $mailer->addEmailInfo($emailInfo);

        // Set all required params and send emails
        $mailer->setSender(Mage::getStoreConfig($sender, $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId(Mage::getStoreConfig($template, $storeId));
        $mailer->setTemplateParams($templateParams);
        $mailer->send();
        return $this;
    }
}