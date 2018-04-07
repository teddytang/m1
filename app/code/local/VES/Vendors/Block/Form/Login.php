<?php
/**
 * Vendor login form block
 *
 * @category   	VES
 * @package    	VES_Vendors
 * @author      Vnecoms Team <support@vnecoms.com>
 */
class VES_Vendors_Block_Form_Login extends Mage_Core_Block_Template
{
    private $_username = -1;

    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('head')->setTitle(Mage::helper('customer')->__('Vendor Login'));
        return parent::_prepareLayout();
    }

    /**
     * Retrieve form posting url
     *
     * @return string
     */
    public function getPostActionUrl()
    {
        return $this->helper('vendors')->getLoginPostUrl();
    }

    /**
     * Retrieve create new account url
     *
     * @return string
     */
    public function getCreateAccountUrl()
    {
        $url = $this->getData('create_account_url');
        if (is_null($url)) {
            $url = $this->helper('vendors')->getRegisterUrl();
        }
        return $url;
    }

    /**
     * Retrieve password forgotten url
     *
     * @return string
     */
    public function getForgotPasswordUrl()
    {
        return $this->helper('vendors')->getForgotPasswordUrl();
    }

    /**
     * Retrieve username for form field
     *
     * @return string
     */
    public function getUsername()
    {
        if (-1 === $this->_username) {
            $this->_username = Mage::getSingleton('vendors/session')->getUsername(true);
        }
        return $this->_username;
    }
    
    public function canRegister(){
    	return Mage::getStoreConfig('vendors/create_account/register');
    }
    
    public function isForgotPassword(){
    	return Mage::registry('is_forgot_password');
    }
}
