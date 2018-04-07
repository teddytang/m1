<?php

class VES_Vendors_Model_Vendor extends Mage_Core_Model_Abstract
{
	const MODE_GENERAL 			= 'general';
	const MODE_ADVANCED			= 'advanced';
	const MODE_ADVANCED_X		= 'advanced_x';
	
	const ENTITY                 	= 'ves_vendor';
	const XML_PATH_IS_CONFIRM		= 'vendors/create_account/confirm';
	
	const XML_PATH_REGISTER_EMAIL_TEMPLATE 		= 'vendors/create_account/email_template';
    const XML_PATH_REGISTER_EMAIL_IDENTITY 		= 'vendors/create_account/email_identity';
	const XML_PATH_CONFIRM_EMAIL_TEMPLATE       = 'vendors/create_account/email_confirmation_template';
    const XML_PATH_CONFIRMED_EMAIL_TEMPLATE     = 'vendors/create_account/email_confirmed_template';
	const XML_PATH_ACTIVE_EMAIL_TEMPLATE		= 'vendors/create_account/email_template_approved';
    
	const XML_PATH_FORGOT_EMAIL_TEMPLATE 		= 'vendors/password/forgot_email_template';
    const XML_PATH_FORGOT_EMAIL_IDENTITY 		= 'vendors/password/forgot_email_identity';
    
	const STATUS_PENDING	= 0;
	const STATUS_ACTIVATED	= 1;
    const STATUS_DISABLED	= 2;
    
	/**
     * Codes of exceptions related to customer model
     */
    const EXCEPTION_EMAIL_NOT_CONFIRMED       	= 1;
    const EXCEPTION_INVALID_EMAIL_OR_PASSWORD 	= 2;
    const EXCEPTION_EMAIL_EXISTS              	= 3;
    const EXCEPTION_VENDOR_ID_EXISTS          	= 4;
    const EXCEPTION_ACCOUNT_SUPPENDED			= 5;
    const EXCEPTION_ACCOUNT_PENDING				= 6;
    const EXCEPTION_VENDOR_ID_NOT_ACCEPTED		= 7;
    
    const EXCEPTION_INVALID_RESET_PASSWORD_LINK_TOKEN = 6;
	/**
     * Confirmation requirement flag
     *
     * @var boolean
     */
    private static $_isConfirmationRequired;
    protected $_eventPrefix 	= 'vendor';
    protected $_eventObject		= 'vendor';
    
    
    public function _construct()
    {
        parent::_construct();
        $this->_init('vendors/vendor');
    }
    /**
     * Processing object before save data
     *
     * @return Mage_Customer_Model_Customer
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        $storeId = $this->getStoreId();
        if ($storeId === null) {
            $this->setStoreId(Mage::app()->getStore()->getId());
        }

        $this->getGroupId();
        return $this;
    }

    /**
     * Get Group object
     * @return VES_Vendors_Model_Group
     */
    public function getGroup(){
    	if (!$this->hasData('vendor_group')) {
    		$this->setData('vendor_group',Mage::getModel('vendors/group')->load($this->getGroupId()));
    	}
    	return $this->getData('vendor_group');
    }
	/**
     * Validate vendor attribute values.
     * For existing customer password + confirmation will be validated only when password is set (i.e. its change is requested)
     *
     * @return bool
     */
    public function validate()
    {
        $errors = array();
        if (!Zend_Validate::is( trim($this->getFirstname()) , 'NotEmpty')) {
            $errors[] = Mage::helper('customer')->__('The first name cannot be empty.');
        }

        if (!Zend_Validate::is( trim($this->getLastname()) , 'NotEmpty')) {
            $errors[] = Mage::helper('customer')->__('The last name cannot be empty.');
        }

        if (!Zend_Validate::is($this->getEmail(), 'EmailAddress')) {
            $errors[] = Mage::helper('customer')->__('Invalid email address "%s".', $this->getEmail());
        }

        $password = $this->getPassword();
        if (!$this->getId() && !Zend_Validate::is($password , 'NotEmpty')) {
            $errors[] = Mage::helper('customer')->__('The password cannot be empty.');
        }
        if (strlen($password) && !Zend_Validate::is($password, 'StringLength', array(6))) {
            $errors[] = Mage::helper('customer')->__('The minimum password length is %s', 6);
        }
        $confirmation = $this->getConfirmation();
        if ($password != $confirmation) {
            $errors[] = Mage::helper('customer')->__('Please make sure your passwords match.');
        }

        if (empty($errors)) {
            return true;
        }
        return $errors;
    }
    
    /**
     * Check if accounts confirmation is required in config
     *
     * @return bool
     */
    public function isConfirmationRequired()
    {
        if (self::$_isConfirmationRequired === null) {
            $storeId = $this->getStoreId() ? $this->getStoreId() : null;
            self::$_isConfirmationRequired = (bool)Mage::getStoreConfig(self::XML_PATH_IS_CONFIRM, $storeId);
        }

        return self::$_isConfirmationRequired;
    }
/**
     * Set plain and hashed password
     *
     * @param string $password
     * @return Mage_Customer_Model_Customer
     */
    public function setPassword($password)
    {
        $this->setData('password', $password);
        $this->setPasswordHash($this->hashPassword($password));
        return $this;
    }

    /**
     * Hash customer password
     *
     * @param   string $password
     * @param   int    $salt
     * @return  string
     */
    public function hashPassword($password, $salt = null)
    {
        return Mage::helper('core')->getHash($password, !is_null($salt) ? $salt : 2);
    }

    /**
     * Retrieve random password
     *
     * @param   int $length
     * @return  string
     */
    public function generatePassword($length = 8)
    {
        $chars = Mage_Core_Helper_Data::CHARS_PASSWORD_LOWERS
            . Mage_Core_Helper_Data::CHARS_PASSWORD_UPPERS
            . Mage_Core_Helper_Data::CHARS_PASSWORD_DIGITS
            . Mage_Core_Helper_Data::CHARS_PASSWORD_SPECIALS;
        return Mage::helper('core')->getRandomString($length, $chars);
    }
	/**
     * Send email with new account related information
     *
     * @param string $type
     * @param string $backUrl
     * @param string $storeId
     * @throws Mage_Core_Exception
     * @return Mage_Customer_Model_Customer
     */
    public function sendNewAccountEmail($type = 'registered', $backUrl = '', $storeId = '0')
    {
        $types = array(
            'registered'   => self::XML_PATH_REGISTER_EMAIL_TEMPLATE,  // welcome email, when confirmation is disabled
            'confirmed'    => self::XML_PATH_CONFIRMED_EMAIL_TEMPLATE, // welcome email, when confirmation is enabled
            'confirmation' => self::XML_PATH_CONFIRM_EMAIL_TEMPLATE,   // email with confirmation link
			'active' => self::XML_PATH_ACTIVE_EMAIL_TEMPLATE,  // Active email, when Vendor is active
        );
        if (!isset($types[$type])) {
            Mage::throwException(Mage::helper('customer')->__('Wrong transactional account email type'));
        }

        if (!$storeId) {
            $storeId = $this->_getWebsiteStoreId($this->getSendemailStoreId());
        }

        $this->_sendEmailTemplate($types[$type], self::XML_PATH_REGISTER_EMAIL_IDENTITY,
            array('vendor' => $this, 'back_url' => $backUrl), $storeId);
        return $this;
    }
	/**
     * Send corresponding email template
     *
     * @param string $emailTemplate configuration path of email template
     * @param string $emailSender configuration path of email identity
     * @param array $templateParams
     * @param int|null $storeId
     * @return Mage_Customer_Model_Customer
     */
    protected function _sendEmailTemplate($template, $sender, $templateParams = array(), $storeId = null)
    {
        /** @var $mailer Mage_Core_Model_Email_Template_Mailer */
        $mailer = Mage::getModel('core/email_template_mailer');
        $emailInfo = Mage::getModel('core/email_info');
        $emailInfo->addTo($this->getEmail(), $this->getName());
        $mailer->addEmailInfo($emailInfo);

        // Set all required params and send emails
        $mailer->setSender(Mage::getStoreConfig($sender, $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId(Mage::getStoreConfig($template, $storeId));
        $mailer->setTemplateParams($templateParams);
        $mailer->send();
        return $this;
    }
	/**
     * Get either first store ID from a set website or the provided as default
     *
     * @param int|string|null $storeId
     *
     * @return int
     */
    protected function _getWebsiteStoreId($defaultStoreId = null)
    {
        if ($this->getWebsiteId() != 0 && empty($defaultStoreId)) {
            $storeIds = Mage::app()->getWebsite($this->getWebsiteId())->getStoreIds();
            reset($storeIds);
            $defaultStoreId = current($storeIds);
        }
        return $defaultStoreId;
    }
	/**
     * Send email with reset password confirmation link
     *
     * @return Mage_Customer_Model_Customer
     */
    public function sendPasswordResetConfirmationEmail()
    {
        $storeId = $this->getStoreId();
        if (!$storeId) {
            $storeId = $this->_getWebsiteStoreId();
        }
		
        $this->_sendEmailTemplate(self::XML_PATH_FORGOT_EMAIL_TEMPLATE, self::XML_PATH_FORGOT_EMAIL_IDENTITY,
            array('vendor' => $this), $storeId);

        return $this;
    }
	/**
     * Change reset password link token
     *
     * Stores new reset password link token
     *
     * @param string $newResetPasswordLinkToken
     * @return VES_Vendors_Model_Vendor
     */
    public function changeResetPasswordLinkToken($newResetPasswordLinkToken) {
        if (!is_string($newResetPasswordLinkToken) || empty($newResetPasswordLinkToken)) {
            throw Mage::exception('Mage_Core', Mage::helper('customer')->__('Invalid password reset token.'),
                self::EXCEPTION_INVALID_RESET_PASSWORD_LINK_TOKEN);
        }
        $this->_getResource()->changeResetPasswordLinkToken($this, $newResetPasswordLinkToken);
        return $this;
    }
	/**
     * Check if current reset password link token is expired
     *
     * @return boolean
     */
    public function isResetPasswordLinkTokenExpired()
    {
        $resetPasswordLinkToken = $this->getRpToken();
        $resetPasswordLinkTokenCreatedAt = $this->getRpTokenCreatedAt();

        if (empty($resetPasswordLinkToken) || empty($resetPasswordLinkTokenCreatedAt)) {
            return true;
        }

        $tokenExpirationPeriod = Mage::helper('vendors')->getResetPasswordLinkExpirationPeriod();

        $currentDate = Varien_Date::now();
        $currentTimestamp = Varien_Date::toTimestamp($currentDate);
        $tokenTimestamp = Varien_Date::toTimestamp($resetPasswordLinkTokenCreatedAt);

        if ($tokenTimestamp > $currentTimestamp) {
            return true;
        }
        $dayDifference = floor(($currentTimestamp - $tokenTimestamp) / (24 * 60 * 60));
        if ($dayDifference >= $tokenExpirationPeriod) {
            return true;
        }
        return false;
    }
    /**
     * Generate random confirmation key
     *
     * @return string
     */
    public function getRandomConfirmationKey()
    {
        return md5(uniqid());
    }
    /**
     * Retrieve customer sharing configuration model
     *
     * @return Mage_Customer_Model_Config_Share
     */
    public function getSharingConfig()
    {
        return Mage::getSingleton('vendors/config_share');
    }
    
	/**
     * Authenticate customer
     *
     * @param  string $login (email, vendor_id)
     * @param  string $password
     * @throws Mage_Core_Exception
     * @return true
     *
     */
    public function authenticate($login, $password)
    {
    	/*Check if login is email*/
    	$validator = new Zend_Validate_EmailAddress();
    	if($validator->isValid($login)) {
    		$this->loadByEmail($login);
    	}else{
    		$this->loadByVendorId($login);
    	}
        
        if (!$this->validatePassword($password)) {
            throw Mage::exception('Mage_Core', Mage::helper('vendors')->__('Invalid login or password.'),
                self::EXCEPTION_INVALID_EMAIL_OR_PASSWORD
            );
        }

        if ($this->getConfirmation() && $this->isConfirmationRequired()) {
            throw Mage::exception('Mage_Core', Mage::helper('vendors')->__('Your vendor account is not confirmed.'),
                self::EXCEPTION_EMAIL_NOT_CONFIRMED
            );
        }
        
        if ($this->getStatus() == self::STATUS_PENDING){
        	throw Mage::exception('Mage_Core', Mage::helper('vendors')->__('Your vendor account is awaiting for approval.'),
                self::EXCEPTION_ACCOUNT_SUPPENDED
            );
        }
        
    	if ($this->getStatus() == self::STATUS_DISABLED) {
            throw Mage::exception('Mage_Core', Mage::helper('vendors')->__('Your vendor account has been suppended.'),
                self::EXCEPTION_ACCOUNT_SUPPENDED
            );
        }
        
        Mage::dispatchEvent('customer_customer_authenticated', array(
           'model'    => $this,
           'password' => $password,
        ));

        return true;
    }
	/**
     * Load vendor by email
     *
     * @param   string $vendorEmail
     * @return  VES_Vendor_Model_Vendor
     */
    public function loadByEmail($vendorEmail,$websiteId=false)
    {
    	if(!$websiteId) $websiteId = Mage::app()->getWebsite()->getId();
    	if($websiteId) $this->setWebsiteId($websiteId);
        $this->_getResource()->loadByEmail($this, $vendorEmail);
        return $this;
    }
	/**
     * Load vendor by vendor id
     *
     * @param   string $vendorId
     * @return  VES_Vendor_Model_Vendor
     */
    public function loadByVendorId($vendorId,$websiteId=false)
    {
    	if(!$websiteId) $websiteId = Mage::app()->getWebsite()->getId();
    	if($websiteId) $this->setWebsiteId($websiteId);
        $this->_getResource()->loadByVendorId($this, $vendorId);
        return $this;
    }
    
    /**
     * Load vendor by attribute value
     * @param unknown_type $attributeCode
     * @param unknown_type $attributeValue
     * @param unknown_type $websiteId
     */
    public function loadByAttribute($attributeCode, $attributeValue,$websiteId=false){
    	if(!$websiteId) $websiteId = Mage::app()->getWebsite()->getId();
    	if($websiteId) {
    		$collection = $this->getCollection()->addAttributeToSelect('*')->addAttributeToFilter($attributeCode,$attributeValue);
    		if($collection->count()){
    			return $collection->getFirstItem();
    		}
    	}
		$this->setData(null);
    	return $this;
    }
	/**
     * Validate password with salted hash
     *
     * @param string $password
     * @return boolean
     */
    public function validatePassword($password)
    {
        $hash = $this->getPasswordHash();
        if (!$hash) {
            return false;
        }
        return Mage::helper('core')->validateHash($password, $hash);
    }
    
    public function getUsername(){
    	return $this->getVendorId();
    }
    
    function getName(){
    	return $this->getFirstname().' '.$this->getLastname();
    }
    
    public function getCountryName(){
    	return Mage::getModel("directory/country")->load($this->getCountryId())->getName();
    }
    
    public function getRegion(){
    	if($this->getRegionId()){
    		$region = Mage::getModel('directory/region')->load($this->getRegionId());
    		return $region->getName();
    	}
    	return $this->getData('region');
    }
}