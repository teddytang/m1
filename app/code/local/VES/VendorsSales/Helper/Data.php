<?php
class VES_VendorsSales_Helper_Data extends Mage_Core_Helper_Abstract
{
	const XML_PATH_EMAIL_TEMPLATE 			= 'vendors/sales/order_new_template';
    const XML_PATH_EMAIL_IDENTITY			= 'vendors/sales/email_identity';
    
    /**
     * Can send new order notification email 
     * @param int $storeId
     * @return boolean
     */
    public function canSendNewOrderEmail($storeId){
    	return Mage::getStoreConfig('vendors/sales/order_new_enable',$storeId);
    }
    
    /**
     * Can see payment info
     * @return boolean
     */
    public function canSeePaymentInfo(){
    	return Mage::getStoreConfig('vendors/sales/view_payment_info');
    }
    
	/**
     * Can see billing/shipping address
     * @return boolean
     */
    public function canSeeAddress(){
    	return Mage::getStoreConfig('vendors/sales/view_addresses');
    }
    
    /**
     * Can see invoice
     * @return boolean
     */
	public function canSeeInvoice(){
    	return Mage::getStoreConfig('vendors/sales/view_invoices');
    }
    
    /**
     * Can see credit memo
     * @return boolean
     */
    public function canSeeCreditMemo(){
    	return Mage::getStoreConfig('vendors/sales/view_creditmemo');
    }
    
    /**
     * Send new order notification email to vendor
     * @param Mage_Sales_Model_Order $order
     */
	public function sendNewOrderEmail(Mage_Sales_Model_Order $order){
		$storeId = $order->getStore()->getId();
		if (!$this->canSendNewOrderEmail($storeId)) {
            return $this;
        }
		$vendorId = $order->getVendorId();
		$isAdvancedMode = Mage::helper('vendors')->isAdvancedMode();
		if($isAdvancedMode){
			if(!$vendorId) return;
			$vendor = Mage::getModel('vendors/vendor')->load($vendorId);
			if(!$vendorId || !$vendor->getId()){
				return $this;
			}
	        try {
	            // Retrieve specified view block from appropriate design package (depends on emulated store)
	            $paymentBlock = Mage::helper('payment')->getInfoBlock($order->getPayment())
	                ->setIsSecureMode(true);
	            $paymentBlock->getMethod()->setStore($storeId);
	            $paymentBlockHtml = $paymentBlock->toHtml();
	        } catch (Exception $exception) {
	            throw $exception;
	        }
	
			$this->_sendEmailTemplate(self::XML_PATH_EMAIL_TEMPLATE, self::XML_PATH_EMAIL_IDENTITY,
	            array('vendor' => $vendor,'order' => $order, 'billing' => $order->getBillingAddress(),'payment_html'=>$paymentBlockHtml),null,$vendor);
		}else{
			$vendorIds = array();
			foreach($order->getAllItems() as $item){
				if(!in_array($item->getVendorId(), $vendorIds)) $vendorIds[] = $item->getVendorId();
			}
			
			try {
	            // Retrieve specified view block from appropriate design package (depends on emulated store)
	            $paymentBlock = Mage::helper('payment')->getInfoBlock($order->getPayment())
	                ->setIsSecureMode(true);
	            $paymentBlock->getMethod()->setStore($storeId);
	            $paymentBlockHtml = $paymentBlock->toHtml();
	        } catch (Exception $exception) {
	            throw $exception;
	        }
	        
			foreach($vendorIds as $vendorId){
				$vendor = Mage::getModel('vendors/vendor')->load($vendorId);
				if(!$vendor->getId()){
					continue;
				}

				$this->_sendEmailTemplate(self::XML_PATH_EMAIL_TEMPLATE, self::XML_PATH_EMAIL_IDENTITY,
		            array('vendor' => $vendor,'order' => $order, 'billing' => $order->getBillingAddress(),'payment_html'=>$paymentBlockHtml),null,$vendor);
			}
		}
		
	}
	
	/**
	 * Send shipment email
	 * @param Mage_Sales_Model_Order_Shipment $shipment
	 * @param unknown_type $notifyCustomer
	 * @param unknown_type $comment
	 */
	public function sendShipmentEmail(Mage_Sales_Model_Order_Shipment $shipment,$notifyCustomer = true, $comment = ''){
		$shipment->sendEmail($notifyCustomer, $comment);
        return $shipment;
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