<?php

/**
 * Vendor header block
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <core@magentocommerce.com>
 */
class VES_Vendors_Block_Page_Header extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('ves_vendors/page/header.phtml');
    }

    public function getHomeLink()
    {
        return $this->helper('vendors')->getDashboardUrl();
    }

    public function getUser()
    {
        return Mage::getSingleton('vendors/session')->getVendor();
    }

    public function getLogoutLink()
    {
        return $this->getUrl('vendors/account/logout');
    }

    /**
     * Check if noscript notice should be displayed
     *
     * @return boolean
     */
    public function displayNoscriptNotice()
    {
        return Mage::getStoreConfig('web/browser_capabilities/javascript');
    }

}
