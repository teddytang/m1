<?php
/**
 * Vendor session model
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_Vendors_Model_Session extends Mage_Core_Model_Session_Abstract
{
    /**
     * Vendor object
     *
     * @var VES_Vendors_Model_Vendors
     */
    protected $_vendor;

    /**
     * Flag with Vendor id validations result
     *
     * @var bool
     */
    protected $_isVendorIdChecked = null;

    /**
     * Persistent Vendor group id
     *
     * @var null|int
     */
    protected $_persistentVendorGroupId = null;

    /**
     * Retrieve vendor sharing configuration model
     *
     * @return VES_Vendors_Model_Config_Share
     */
    public function getVendorConfigShare()
    {
        return Mage::getSingleton('vendors/config_share');
    }

    public function __construct()
    {
        $namespace = 'vendor';
        if ($this->getVendorConfigShare()->isWebsiteScope()) {
            $namespace .= '_' . (Mage::app()->getStore()->getWebsite()->getCode());
        }

        $this->init($namespace);
        Mage::dispatchEvent('vendor_session_init', array('vendor_session'=>$this));
    }

    /**
     * Set vendor object and setting vendor id in session
     *
     * @param   VES_Vendors_Model_Vendor $vendor
     * @return  VES_Vendors_Model_Session
     */
    public function setVendor(VES_Vendors_Model_Vendor $vendor)
    {
        $this->_vendor = $vendor;
        $this->setId($vendor->getId());
        return $this;
    }

    /**
     * Retrieve vendor model object
     *
     * @return VES_Vendors_Model_Vendor
     */
    public function getVendor()
    {
        if ($this->_vendor instanceof VES_Vendors_Model_Vendor) {
            return $this->_vendor;
        }

        $vendor = Mage::getModel('vendors/vendor')
            ->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
        if ($this->getId()) {
            $vendor->load($this->getId());
        }

        $this->setVendor($vendor);
        return $this->_vendor;
    }
	public function getUser(){
		return $this->getVendor();
	}
    /**
     * Set vendor id
     *
     * @param int|null $id
     * @return VES_Vendors_Model_Session
     */
    public function setVendorId($id)
    {
        $this->setData('vendor_id', $id);
        return $this;
    }

    /**
     * Retrieve vvendor id from current session
     *
     * @return int|null
     */
    public function getVendorId()
    {
        if ($this->getData('vendor_id')) {
            return $this->getData('vendor_id');
        }
        return ($this->isLoggedIn()) ? $this->getId() : null;
    }

    /**
     * Set vendor group id
     *
     * @param int|null $id
     * @return VES_Vendors_Model_Session
     */
    public function setVendorGroupId($id)
    {
        $this->setData('vendor_group_id', $id);
        return $this;
    }

    /**
     * Get vendor group id
     * If vendor is not logged in system, 'not logged in' group id will be returned
     *
     * @return int
     */
    public function getVendorGroupId()
    {
        if ($this->getData('vendor_group_id')) {
            return $this->getData('vendor_group_id');
        }
        if ($this->isLoggedIn() && $this->getVendor()) {
            return $this->getVendor()->getGroupId();
        }
        return VES_Vendors_Model_Group::NOT_LOGGED_IN_ID;
    }

    /**
     * Checking vendor login status
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return (bool)$this->getId() && (bool)$this->checkVendorId($this->getId());
    }

    /**
     * Check exists vendor (light check)
     *
     * @param int $vendorId
     * @return bool
     */
    public function checkVendorId($vendorId)
    {
        if ($this->_isVendorIdChecked === null) {
            $this->_isVendorIdChecked = Mage::getResourceSingleton('vendors/vendor')->checkVendorId($vendorId);
        }
        return $this->_isVendorIdChecked;
    }

    /**
     * vendor authorization
     *
     * @param   string $username
     * @param   string $password
     * @return  bool
     */
    public function login($username, $password)
    {
        /** @var $vendor VES_Vendors_Model_Vendor */
        $vendor = Mage::getModel('vendors/vendor')
            ->setWebsiteId(Mage::app()->getStore()->getWebsiteId());

        if ($vendor->authenticate($username, $password)) {
            $this->setVendorAsLoggedIn($vendor);
            $this->renewSession();
            return true;
        }
        return false;
    }

    public function setVendorAsLoggedIn($vendor)
    {
        $this->setVendor($vendor);
        Mage::dispatchEvent('vendor_login', array('vendor'=>$vendor));
        return $this;
    }

    /**
     * Authorization vendor by identifier
     *
     * @param   int $vendorId
     * @return  bool
     */
    public function loginById($vendorId)
    {
        $vendor = Mage::getModel('vendors/vendor')->load($vendorId);
        if ($vendor->getId()) {
            $this->setVendorAsLoggedIn($vendor);
            return true;
        }
        return false;
    }

    /**
     * Logout vendor
     *
     * @return VES_Vendors_Model_Session
     */
    public function logout()
    {
        if ($this->isLoggedIn()) {
            Mage::dispatchEvent('vendor_logout', array('vendor' => $this->getVendor()) );
            $this->_logout();
        }
        return $this;
    }

    /**
     * Authenticate controller action by login vendor
     *
     * @param   Mage_Core_Controller_Varien_Action $action
     * @param   bool $loginUrl
     * @return  bool
     */
    public function authenticate(Mage_Core_Controller_Varien_Action $action, $loginUrl = null)
    {
        if ($this->isLoggedIn()) {
            return true;
        }

        $this->setBeforeAuthUrl(Mage::getUrl('*/*/*', array('_current' => true)));
        if (isset($loginUrl)) {
            $action->getResponse()->setRedirect($loginUrl);
        } else {
            $action->setRedirectWithCookieCheck(VES_Vendors_Helper_Data::ROUTE_ACCOUNT_LOGIN,
                Mage::helper('vendors')->getLoginUrlParams()
            );
        }

        return false;
    }

    /**
     * Set auth url
     *
     * @param string $key
     * @param string $url
     * @return VES_Vendors_Model_Session
     */
    protected function _setAuthUrl($key, $url)
    {
        $url = Mage::helper('core/url')
            ->removeRequestParam($url, Mage::getSingleton('core/session')->getSessionIdQueryParam());
        // Add correct session ID to URL if needed
        $url = Mage::getModel('core/url')->getRebuiltUrl($url);
        return $this->setData($key, $url);
    }

    /**
     * Logout without dispatching event
     *
     * @return VES_Vendors_Model_Session
     */
    protected function _logout()
    {
        $this->setId(null);
        $this->setVendorGroupId(VES_Vendors_Model_Group::NOT_LOGGED_IN_ID);
        $this->getCookie()->delete($this->getSessionName());
        return $this;
    }

    /**
     * Set Before auth url
     *
     * @param string $url
     * @return VES_Vendors_Model_Session
     */
    public function setBeforeAuthUrl($url)
    {
        return $this->_setAuthUrl('before_auth_url', $url);
    }

    /**
     * Set After auth url
     *
     * @param string $url
     * @return VES_Vendors_Model_Session
     */
    public function setAfterAuthUrl($url)
    {
        return $this->_setAuthUrl('after_auth_url', $url);
    }

    /**
     * Reset core session hosts after reseting session ID
     *
     * @return VES_Vendors_Model_Session
     */
    public function renewSession()
    {
        parent::renewSession();
        Mage::getSingleton('core/session')->unsSessionHosts();

        return $this;
    }
}
