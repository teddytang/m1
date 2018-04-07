<?php

class VES_VendorsProduct_Helper_Data extends Mage_Core_Helper_Abstract
{
	const XML_PATH_PRODUCT_APPROVED_TEMPLATE 		= 'vendors/catalog/product_approved_template';
	const XML_PATH_PRODUCT_REJECTED_TEMPLATE 		= 'vendors/catalog/product_rejected_template';
    const XML_PATH_PRODUCT_REVIEWED_IDENTITY 		= 'vendors/catalog/email_identity';
    
	public function getCategoryAjaxUrl(){
		return Mage::getUrl('vendors/catalog/product_vendorcategory');
	}
	
	public function isProductApproval(){
		return Mage::getStoreConfig('vendors/catalog/product_approval');
	}
	
	public function sendProductReviewdNotificationEmail(Mage_Catalog_Model_Product $product,$status){
		$types = array(
            VES_VendorsProduct_Model_Source_Approval::STATUS_APPROVED   => self::XML_PATH_PRODUCT_APPROVED_TEMPLATE,  // Notification email when product is approved
            VES_VendorsProduct_Model_Source_Approval::STATUS_UNAPPROVED => self::XML_PATH_PRODUCT_REJECTED_TEMPLATE,  // // Notification email when product is rejected
        );
        $vendor = Mage::getModel('vendors/vendor')->load($product->getVendorId());
        if(!$vendor->getId()) return;
        
        $this->_sendEmailTemplate($types[$status], self::XML_PATH_PRODUCT_REVIEWED_IDENTITY,
            array('vendor' => $vendor, 'product' => $product),null,$vendor);
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
    
	public function formatUrlKey($str)
    {
        $str = Mage::helper('core')->removeAccents($str);
        $urlKey = preg_replace('#[^0-9a-z]+#i', '-', $str);
        $urlKey = strtolower($urlKey);
        $urlKey = trim($urlKey, '-');
        return $urlKey;
    }
    /**
     * These product attributes will not be displayed on vendor cpanel.
     * @return array
     */
    public function getRestrictionProductAttribute(){
        return array_keys(Mage::app()->getConfig()->getNode('product_attribute_restriction')->asArray());
    }
    
    /**
     * There product attributes will not be able to removed from the attribute set.
     * @return array:
     */
    public function getRequiredProductAttributes(){
        return array_keys(Mage::app()->getConfig()->getNode('product_required_attributes')->asArray());
    }
}