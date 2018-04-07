<?php
class VES_Vendors_IndexController extends VES_Vendors_Controller_Action
{
	/**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch()
    {
        // a brute-force protection here would be nice

        parent::preDispatch();

        if (!$this->getRequest()->isDispatched()) {
            return;
        }

        $action = $this->getRequest()->getActionName();
        $openActions = array(
        	'create',
        	'createPost',
            'login',
        	'resetpassword',
            'resetpasswordpost',
            'confirm',
            'confirmation',
        	'forgotPasswordPost',
        );
        $controller = $this->getRequest()->getControllerName();
        $pattern = '/^(' . implode('|', $openActions) . ')/i';

        if (!preg_match($pattern, $action) || $controller!='index') {
            if (!$this->_getSession()->authenticate($this)) {
                $this->setFlag('', 'no-dispatch', true);
            }
        } else {
            $this->_getSession()->setNoReferer(true);
        }
        Mage::getSingleton('core/design_package')->setArea('adminhtml')->setPackageName(Mage_Core_Model_Design_Package::DEFAULT_PACKAGE)->setTheme(Mage_Core_Model_Design_Package::DEFAULT_THEME);
    }
    
	public function indexAction(){
		$this->_redirectUrl(Mage::helper('vendors')->getDashboardUrl());
	}
	/**
     * vendor login form page
     */
    public function loginAction()
    {
        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/dashboard');
            return;
        }
        Mage::register('is_forgot_password',$this->getRequest()->getParam('forgotpass',false));
        $this->_title(Mage::helper('vendors')->__('Login'));
        $this->getResponse()->setHeader('Login-Required', 'true');
        $this->loadLayout();
        $this->_initLayoutMessages('vendors/session');
        $this->_initLayoutMessages('catalog/session');
        $this->renderLayout();
    }
	/**
     * Login post action
     */
    public function loginPostAction()
    {
        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        $session = $this->_getSession();

        if ($this->getRequest()->isPost()) {
            $login = $this->getRequest()->getPost('login');
            if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    $session->login($login['username'], $login['password']);
                    if ($session->getVendor()->getIsJustConfirmed()) {
                        $this->_welcomeVendor($session->getVendor(), true);
                    }
                } catch (Mage_Core_Exception $e) {
                    switch ($e->getCode()) {
                        case VES_Vendors_Model_Vendor::EXCEPTION_EMAIL_NOT_CONFIRMED:
                            $value = Mage::helper('vendors')->getEmailConfirmationUrl($login['username']);
                            $message = Mage::helper('vendors')->__('Your vendor account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', $value);
                            break;
                        case VES_Vendors_Model_Vendor::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                            $message = $e->getMessage();
                            break;
                        default:
                            $message = $e->getMessage();
                    }
                    $session->addError($message);
                    $session->setUsername($login['username']);
                } catch (Exception $e) {
                    // Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
                }
            } else {
                $session->addError($this->__('Login and password are required.'));
            }
        }

        $this->_loginPostRedirect();
    }
	/**
     * Define target URL and redirect customer after logging in
     */
    protected function _loginPostRedirect()
    {
        $session = $this->_getSession();

        if (!$session->getBeforeAuthUrl() || $session->getBeforeAuthUrl() == Mage::getBaseUrl()) {
            // Set default URL to redirect customer to
            $session->setBeforeAuthUrl(Mage::helper('vendors')->getAccountUrl());
            // Redirect customer to the last page visited after logging in
            if ($session->isLoggedIn()) {
                if ($session->getAfterAuthUrl()) {
                    $session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
                }
            } else {
                $session->setBeforeAuthUrl(Mage::helper('vendors')->getLoginUrl());
            }
        } else if ($session->getBeforeAuthUrl() == Mage::helper('vendors')->getLogoutUrl()) {
            $session->setBeforeAuthUrl(Mage::helper('vendors')->getDashboardUrl());
        } else {
            if (!$session->getAfterAuthUrl()) {
                $session->setAfterAuthUrl($session->getBeforeAuthUrl());
            }
            if ($session->isLoggedIn()) {
                $session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
            }
        }
        $this->_redirectUrl($session->getBeforeAuthUrl(true));
    }
    
    public function createAction(){
    	if(!Mage::getStoreConfig('vendors/create_account/register')){
    		$this->_forward('no-route');
    		return;
    	}
    	if(Mage::getSingleton('vendors/session')->getVendorId()){$this->_redirect('*/*');return;}
    	
    	$this->loadLayout()->_setPageTitle('Vendor Register');
    	$this->renderLayout();
    }
    
	/**
     * Create Vendor account action
     */
    public function createPostAction()
    {
    	if(!Mage::getStoreConfig('vendors/create_account/register')){
    		$this->_forward('no-route');
    		return;
    	}
        $session = $this->_getSession();
        if ($session->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        $session->setEscapeMessages(true); // prevent XSS injection in user input
        if ($this->getRequest()->isPost()) {
            $errors = array();

            if (!$vendor = Mage::registry('current_vendor')) {
                $vendor = Mage::getModel('vendors/vendor')->setId(null);
            }

            /**
             * Initialize customer group id
             */
            $vendor->getGroupId();
			
            try {
            	$data = $this->getRequest()->getPost();
	            if(isset($_FILES['logo']['name']) && $_FILES['logo']['name'] != '') {
					try {
						/* Starting upload */	
						$uploader = new Varien_File_Uploader('logo');
		           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
						$uploader->setAllowRenameFiles(true);
						$uploader->setFilesDispersion(true);
						$path = Mage::getBaseDir('media') . DS."ves_vendors".DS."logo".DS ;
						$uploader->save($path, $_FILES['logo']['name']);
						$data['logo'] = "ves_vendors/logo".$uploader->getUploadedFileName();
					} catch (Exception $e) {
			      		
			        }
				}else{
					if(isset($data['logo']['delete']) && $data['logo']['delete']){
						$data['logo'] = '';
					}else{
						$data['logo'] = $data['logo']['value'];
					}
				}
            	$vendor->setData($data);
	        	$vendor->setPassword($this->getRequest()->getPost('password'));
	        	$vendor->setConfirmation($this->getRequest()->getPost('confirmation'));
	        	$customerErrors = $vendor->validate();
	        	if (is_array($customerErrors)) {
	        		$errors = array_merge($customerErrors, $errors);
	        	}
                
                $validationResult = count($errors) == 0;

                if (true === $validationResult) {
                	if(!Mage::helper('vendors')->approvalRequired() && !$vendor->isConfirmationRequired()){
                		$vendor->setStatus(VES_Vendors_Model_Vendor::STATUS_ACTIVATED);
                	}
                    else{
                        $vendor->setStatus(VES_Vendors_Model_Vendor::STATUS_PENDING);
                    }
                	$vendor->setGroupId(Mage::getStoreConfig('vendors/create_account/default_group'));
                	Mage::dispatchEvent('vendor_register_before',
                        array('account_controller' => $this, 'vendor' => $vendor)
                    );
                    $vendor->save();

                    Mage::dispatchEvent('vendor_register_success',
                        array('account_controller' => $this, 'vendor' => $vendor)
                    );

                    if ($vendor->isConfirmationRequired()) {
                        $vendor->sendNewAccountEmail(
                            'confirmation',
                            $session->getBeforeAuthUrl(),
                            Mage::app()->getStore()->getId()
                        );
                        $session->addSuccess($this->__('Account confirmation is required. Please, check your email for the confirmation link. To resend the confirmation email please <a href="%s">click here</a>.', Mage::helper('vendors')->getEmailConfirmationUrl($vendor->getEmail())));
                        $this->_redirectSuccess(Mage::getUrl('*/*/index', array('_secure'=>true)));
                        return;
                    } else {
                    	if(Mage::helper('vendors')->approvalRequired()){
                    		$this->_getSession()->addSuccess(
					            $this->__('Thank you for registering with %s. Your vendor account info is submited for approval.', Mage::app()->getStore()->getFrontendName())
					        );
					        $vendor->sendNewAccountEmail('registered','',Mage::app()->getStore()->getId());
	                        $this->_redirectSuccess(Mage::getUrl('*/*/index', array('_secure'=>true)));
                    	}else{
	                        $session->setVendorAsLoggedIn($vendor);
	                        $url = $this->_welcomeVendor($vendor);
	                        $this->_redirectSuccess($url);
                    	}
                        return;
                    }
                } else {
                    $session->setVendorFormData($this->getRequest()->getPost());
                    if (is_array($errors)) {
                        foreach ($errors as $errorMessage) {
                            $session->addError($errorMessage);
                        }
                    } else {
                        $session->addError($this->__('Invalid customer data'));
                    }
                }
            } catch (Mage_Core_Exception $e) {
                $session->setVendorFormData($this->getRequest()->getPost());
                if ($e->getCode() === VES_Vendors_Model_Vendor::EXCEPTION_EMAIL_EXISTS) {
                    $url = Mage::getUrl('vendors/account/forgotpassword');
                    $message = $this->__('There is already an account with this email address. If you are sure that it is your email address, <a href="%s">click here</a> to get your password and access your account.', $url);
                    $session->setEscapeMessages(false);
                }elseif($e->getCode() === VES_Vendors_Model_Vendor::EXCEPTION_VENDOR_ID_EXISTS){
                	$url = Mage::getUrl('vendors/account/forgotpassword');
                    $message = $this->__('There is already an account with this vendor id. If you are sure that it is your email address, <a href="%s">click here</a> to get your password and access your account.', $url);
                    $session->setEscapeMessages(false);
                } else {
                    $message = $e->getMessage();
                }
                $session->addError($message);
            } catch (Exception $e) {
                $session->setVendorFormData($this->getRequest()->getPost())
                    ->addException($e, $this->__('Cannot save the vendor.'));
            }
        }

        $this->_redirectError(Mage::getUrl('*/*/create', array('_secure' => true)));
    }
    
	/**
     * Add welcome message and send new account email.
     * Returns success URL
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param bool $isJustConfirmed
     * @return string
     */
    protected function _welcomeVendor(VES_Vendors_Model_Vendor $vendor, $isJustConfirmed = false)
    {
        $this->_getSession()->addSuccess(
            $this->__('Thank you for registering with %s.', Mage::app()->getStore()->getFrontendName())
        );

        $vendor->sendNewAccountEmail(
            $isJustConfirmed ? 'confirmed' : 'registered',
            '',
            Mage::app()->getStore()->getId()
        );

        $successUrl = Mage::getUrl('*/*/index', array('_secure'=>true));
        if ($this->_getSession()->getBeforeAuthUrl()) {
            $successUrl = $this->_getSession()->getBeforeAuthUrl(true);
        }
        return $successUrl;
    }
    
	/**
     * Confirm customer account by id and confirmation key
     */
    public function confirmAction()
    {
        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        try {
            $id      = $this->getRequest()->getParam('id', false);
            $key     = $this->getRequest()->getParam('key', false);
            $backUrl = $this->getRequest()->getParam('back_url', false);
            if (empty($id) || empty($key)) {
                throw new Exception($this->__('Bad request.'));
            }

            // load customer by id (try/catch in case if it throws exceptions)
            try {
                $vendor = Mage::getModel('vendors/vendor')->load($id);
                if ((!$vendor) || (!$vendor->getId())) {
                    throw new Exception('Failed to load customer by id.');
                }
            }
            catch (Exception $e) {
                throw new Exception($this->__('Wrong customer account specified.'));
            }

            // check if it is inactive
            if ($vendor->getConfirmation()) {
                if ($vendor->getConfirmation() !== $key) {
                    throw new Exception($this->__('Wrong confirmation key.'));
                }

                // activate customer
                try {
                    $vendor->setConfirmation(null)->setForceConfirmed(true);
                    $vendor->save();
                }
                catch (Exception $e) {
                    throw new Exception($this->__('Failed to confirm customer account.'));
                }
                if(Mage::helper('vendors')->approvalRequired()){
					if($vendor->isConfirmationRequired()){
						// log in and send greeting email, then die happy
						//$this->_getSession()->setVendorAsLoggedIn($vendor);
						$successUrl = $this->_welcomeVendor($vendor, true);
						$this->_redirectSuccess($backUrl ? $backUrl : $successUrl);
					}
                	$this->_getSession()->addSuccess(
						$this->__('Thank you for registering with %s. Your vendor account info is submited for approval.', Mage::app()->getStore()->getFrontendName())
					);
					$this->_redirectSuccess(Mage::getUrl('*/*/index', array('_secure'=>true)));
                }
				else {
					if($vendor->isConfirmationRequired()){
						// log in and send greeting email, then die happy
						$this->_getSession()->setVendorAsLoggedIn($vendor);
						$successUrl = $this->_welcomeVendor($vendor, true);
						$this->_redirectSuccess($backUrl ? $backUrl : $successUrl);
					}
				}
                return;
            }

            // die happy
            $this->_redirectSuccess(Mage::getUrl('*/*/index', array('_secure'=>true)));
            return;
        }
        catch (Exception $e) {
            // die unhappy
            $this->_getSession()->addError($e->getMessage());
            $this->_redirectError(Mage::getUrl('*/*/index', array('_secure'=>true)));
            return;
        }
    }
	/**
     * Send confirmation link to specified email
     */
    public function confirmationAction()
    {
        $vendor = Mage::getModel('vendors/vendor');
        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }

        // try to confirm by email
        $email = $this->getRequest()->getParam('email');
        if ($email) {
            try {
            	$isEmail = Zend_Validate::is($email, 'EmailAddress');
            	if($isEmail){
            		$vendor->setWebsiteId(Mage::app()->getStore()->getWebsiteId())->loadByEmail($email);
            	}else{
            		$vendor->setWebsiteId(Mage::app()->getStore()->getWebsiteId())->loadByVendorId($email);
            	}
                if (!$vendor->getId()) {
                    throw new Exception('');
                }
                if ($vendor->getConfirmation()) {
                    $vendor->sendNewAccountEmail('confirmation', '', Mage::app()->getStore()->getId());
                    $this->_getSession()->addSuccess($this->__('Please, check your email for confirmation key.'));
                } else {
                    $this->_getSession()->addSuccess($this->__('This email does not require confirmation.'));
                }
                $this->_getSession()->setUsername($email);
                $this->_redirectSuccess(Mage::getUrl('*/*/index', array('_secure' => true)));
            } catch (Exception $e) {
                $this->_getSession()->addException($e, $this->__('Wrong email.'));
                $this->_redirectError(Mage::getUrl('*/*/*', array('email' => $email, '_secure' => true)));
            }
            return;
        }
        $this->_redirect('*/*/index');
    }
    
/**
     * Forgot customer password action
     */
    public function forgotPasswordPostAction()
    {
    	if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        $email = (string) $this->getRequest()->getPost('email');
        if ($email) {
            /** @var $customer VES_Vendors_Model_Vendor */
        	$isEmail = Zend_Validate::is($email, 'EmailAddress');
            if($isEmail){
            	$vendor = Mage::getModel('vendors/vendor')
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByEmail($email);
            }else{
            	/*Vendor enter his vendor ID*/
            	$vendor = Mage::getModel('vendors/vendor')
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByVendorId($email);
            }

            if ($vendor->getId()) {
                try {
                    $newResetPasswordLinkToken = Mage::helper('vendors')->generateResetPasswordLinkToken();
                    $vendor->changeResetPasswordLinkToken($newResetPasswordLinkToken);
                    $vendor->sendPasswordResetConfirmationEmail();
                } catch (Exception $exception) {
                    $this->_getSession()->addError($exception->getMessage());
                    $this->_redirect('*/*/login',array('forgotpass'=>true));
                    return;
                }
                 $this->_getSession()->addSuccess(Mage::helper('vendors')->__('If there is an account associated with %s you will receive an email with a link to reset your password.', Mage::helper('vendors')->htmlEscape($email)));
            }else{
            	if($isEmail){
            		$this->_getSession()->addSuccess(Mage::helper('vendors')->__("There is no vendor account associate with your email '%s'", Mage::helper('vendors')->htmlEscape($email)));
            	}else{
            		$this->_getSession()->addSuccess(Mage::helper('vendors')->__("There is no vendor account associate with your vendor ID '%s'", Mage::helper('vendors')->htmlEscape($email)));
            	}
            }
            $this->_redirect('*/*/');
            return;
        } else {
            $this->_getSession()->addError($this->__('Please enter your email.'));
            $this->_redirect('*/*/login',array('forgotpass'=>true));
            return;
        }
    }
	/**
     * Display reset forgotten password form
     *
     * User is redirected on this action when he clicks on the corresponding link in password reset confirmation email
     *
     */
    public function resetPasswordAction()
    {
   		if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        $resetPasswordLinkToken = (string) $this->getRequest()->getQuery('token');
        $vendorId = (int) $this->getRequest()->getQuery('id');
        try {
            $this->_validateResetPasswordLinkToken($vendorId, $resetPasswordLinkToken);
            $this->loadLayout();
            // Pass received parameters to the reset forgotten password form
            $this->getLayout()->getBlock('resetPassword')
                ->setVendorId($vendorId)
                ->setResetPasswordLinkToken($resetPasswordLinkToken);
            $this->renderLayout();
        } catch (Exception $exception) {
            $this->_getSession()->addError(Mage::helper('vendors')->__('Your password reset link has expired.'));
            $this->_redirect('*/*/login');
        }
    }
	
	/**
     * Reset forgotten password
     *
     * Used to handle data recieved from reset forgotten password form
     *
     */
    public function resetPasswordPostAction()
    {
    	if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        $resetPasswordLinkToken = (string) $this->getRequest()->getQuery('token');
        $vendorId = (int) $this->getRequest()->getQuery('id');
        $password = (string) $this->getRequest()->getPost('password');
        $passwordConfirmation = (string) $this->getRequest()->getPost('confirmation');

        try {
            $this->_validateResetPasswordLinkToken($vendorId, $resetPasswordLinkToken);
        } catch (Exception $exception) {
            $this->_getSession()->addError(Mage::helper('vendors')->__('Your password reset link has expired.'));
            $this->_redirect('*/*/');
            return;
        }

        $errorMessages = array();
        if (iconv_strlen($password) <= 0) {
            array_push($errorMessages, Mage::helper('vendors')->__('New password field cannot be empty.'));
        }
        /** @var $customer VES_Vendors_Model_Vendor */
        $vendor = Mage::getModel('vendors/vendor')->load($vendorId);

        $vendor->setPassword($password);
        $vendor->setConfirmation($passwordConfirmation);
        $validationErrorMessages = $vendor->validate();
        if (is_array($validationErrorMessages)) {
            $errorMessages = array_merge($errorMessages, $validationErrorMessages);
        }

        if (!empty($errorMessages)) {
            $this->_getSession()->setCustomerFormData($this->getRequest()->getPost());
            foreach ($errorMessages as $errorMessage) {
                $this->_getSession()->addError($errorMessage);
            }
            $this->_redirect('*/*/resetpassword', array(
                'id' => $vendorId,
                'token' => $resetPasswordLinkToken
            ));
            return;
        }

        try {
            // Empty current reset password token i.e. invalidate it
            $vendor->setRpToken(null);
            $vendor->setRpTokenCreatedAt(null);
            $vendor->setConfirmation(null);
            $vendor->save();
            $this->_getSession()->addSuccess(Mage::helper('customer')->__('Your password has been updated.'));
            $this->_redirect('*/*/login');
        } catch (Exception $exception) {
            $this->_getSession()->addException($exception, $this->__('Cannot save a new password.'));
            $this->_redirect('*/*/resetpassword', array(
                'id' => $vendorId,
                'token' => $resetPasswordLinkToken
            ));
            return;
        }
    }
    
	/**
     * Check if password reset token is valid
     *
     * @param int $vendorId
     * @param string $resetPasswordLinkToken
     * @throws Mage_Core_Exception
     */
    protected function _validateResetPasswordLinkToken($vendorId, $resetPasswordLinkToken)
    {
        if (!is_int($vendorId)
            || !is_string($resetPasswordLinkToken)
            || empty($resetPasswordLinkToken)
            || empty($vendorId)
            || $vendorId < 0
        ) {
            throw Mage::exception('Mage_Core', Mage::helper('vendors')->__('Invalid password reset token.'));
        }

        /** @var $vendor VES_Vendors_Model_Vendor */
        $vendor = Mage::getModel('vendors/vendor')->load($vendorId);
        if (!$vendor || !$vendor->getId()) {
            throw Mage::exception('Mage_Core', Mage::helper('vendors')->__('Wrong customer account specified.'));
        }

        $customerToken = $vendor->getRpToken();
        if (strcmp($customerToken, $resetPasswordLinkToken) != 0 || $vendor->isResetPasswordLinkTokenExpired()) {
            throw Mage::exception('Mage_Core', Mage::helper('vendors')->__('Your password reset link has expired.'));
        }
    }
	
    public function changeLocaleAction(){
    	$locale = $this->getRequest()->getParam('locale');
        if ($locale) {
            Mage::getSingleton('vendors/session')->setLocale($locale);
        }
        $this->_redirectReferer();
    }
	
	/**
     * vendor logout action
     */
    public function logoutAction()
    {
        $this->_getSession()->logout()
            ->setBeforeAuthUrl(Mage::getUrl());

        $this->_redirect('*/*/');
    }
}