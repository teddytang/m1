<?php

class VES_Vendors_Model_Resource_Vendor extends Mage_Eav_Model_Entity_Abstract
{
	protected function _construct()
    {
        $resource = Mage::getSingleton('core/resource');
        $this->setType('ves_vendor');
        $this->setConnection(
            $resource->getConnection('vendor_read'),
            $resource->getConnection('vendor_write')
        );
    }
	/**
     * Check vendor scope, email and confirmation key before saving
     *
     * @param VES_Vendors_Model_Vendor $customer
     * @throws Mage_Customer_Exception
     * @return VES_Vendors_Model_Resource_Vendor
     */
    protected function _beforeSave(Varien_Object $vendor)
    {
        parent::_beforeSave($vendor);

        if (!$vendor->getEmail()) {
            throw Mage::exception('VES_Vendors', Mage::helper('vendors')->__('Vendor email is required'));
        }
		
    	if (!$vendor->getVendorId()) {
            throw Mage::exception('VES_Vendors', Mage::helper('vendors')->__('Vendor email is required'));
        }

        $adapter = $this->_getWriteAdapter();
        $bind    = array('email' => $vendor->getEmail());
		$bind1	= array('vendor_id' => $vendor->getVendorId());
        $select = $adapter->select()
            ->from($this->getEntityTable(), array($this->getEntityIdField()))
            ->where('email = :email');
       	$select1 = $adapter->select()
            ->from($this->getEntityTable(), array($this->getEntityIdField()))
            ->where('vendor_id = :vendor_id');
        if ($vendor->getSharingConfig()->isWebsiteScope()) {
            $bind['website_id'] = (int)$vendor->getWebsiteId();
            $bind1['website_id'] = (int)$vendor->getWebsiteId();
            $select->where('website_id = :website_id');
            $select1->where('website_id = :website_id');
        }
        if ($vendor->getId()) {
            $bind['entity_id'] = (int)$vendor->getId();
            $bind1['entity_id'] = (int)$vendor->getId();
            $select->where('entity_id != :entity_id');
            $select1->where('entity_id != :entity_id');
        }

        $result = $adapter->fetchOne($select, $bind);
        if ($result) {
            throw Mage::exception(
                'VES_Vendors', Mage::helper('customer')->__('This vendor email already exists'),
                VES_Vendors_Model_Vendor::EXCEPTION_EMAIL_EXISTS
            );
        }
    	$result1 = $adapter->fetchOne($select1, $bind1);
        if ($result1) {
            throw Mage::exception(
                'VES_Vendors', Mage::helper('customer')->__('This vendor id already exists'),
                VES_Vendors_Model_Vendor::EXCEPTION_VENDOR_ID_EXISTS
            );
        }
        
        
        $categories = Mage::getResourceModel('catalog/category_collection')->addAttributeToFilter('url_key',$vendor->getVendorId());
        if($categories->count()){
        	throw Mage::exception(
                'VES_Vendors', Mage::helper('customer')->__('This vendor id is not accepted'),
                VES_Vendors_Model_Vendor::EXCEPTION_VENDOR_ID_NOT_ACCEPTED
            );
        }
        
        $rounters = Mage::app()->getConfig()->getNode('frontend/routers')->asArray();
        foreach($rounters as $rounter){
        	if(isset($rounter['args']['frontName']) && $vendor->getVendorId() == $rounter['args']['frontName']){
        		throw Mage::exception(
	                'VES_Vendors', Mage::helper('customer')->__('This vendor id is not accepted'),
	                VES_Vendors_Model_Vendor::EXCEPTION_VENDOR_ID_NOT_ACCEPTED
	            );
        	}
        }
        
        $pages = Mage::getModel('cms/page')->getCollection()->addFieldToFilter('identifier',$vendor->getVendorId());
    	if($pages->count()){
        	throw Mage::exception(
                'VES_Vendors', Mage::helper('customer')->__('This vendor id is not accepted'),
                VES_Vendors_Model_Vendor::EXCEPTION_VENDOR_ID_NOT_ACCEPTED
            );
        }
        
    	// set confirmation key logic
        if ($vendor->getForceConfirmed() || ($vendor->getStatus()==VES_Vendors_Model_Vendor::STATUS_ACTIVATED)) {
            $vendor->setConfirmation(null);
            if(!Mage::helper('vendors')->approvalRequired()){
            	$vendor->setStatus(VES_Vendors_Model_Vendor::STATUS_ACTIVATED);
            }
        } elseif (!$vendor->getId() && $vendor->isConfirmationRequired()) {
            $vendor->setConfirmation($vendor->getRandomConfirmationKey());
            $vendor->setStatus(VES_Vendors_Model_Vendor::STATUS_PENDING);
        }
        
        return $this;
    }
	/**
     * Check vendor by id
     *
     * @param int $vendorId
     * @return bool
     */
    public function checkVendorId($vendorId)
    {
        $adapter = $this->_getReadAdapter();
        $bind    = array('entity_id' => (int)$vendorId);
        $select  = $adapter->select()
            ->from($this->getTable('vendors/vendor'), 'entity_id')
            ->where('entity_id = :entity_id')
            ->limit(1);

        $result = $adapter->fetchOne($select, $bind);
        if ($result) {
            return true;
        }
        return false;
    }
    
    
	/**
     * Load vendor by email
     *
     * @throws Mage_Core_Exception
     *
     * @param VES_Vendors_Model_Vendor $vendor
     * @param string $email
     * @param bool $testOnly
     * @return VES_Vendors_Model_Resource_Vendor
     */
    public function loadByEmail(VES_Vendors_Model_Vendor $vendor, $email, $testOnly = false)
    {
        $adapter = $this->_getReadAdapter();
        $bind    = array('vendor_email' => $email);
        $select  = $adapter->select()
            ->from($this->getEntityTable(), array($this->getEntityIdField()))
            ->where('email = :vendor_email');

        if ($vendor->getSharingConfig()->isWebsiteScope()) {
            if (!$vendor->hasData('website_id')) {
                Mage::throwException(
                    Mage::helper('vendors')->__('Vendor website ID must be specified when using the website scope')
                );
            }
            $bind['website_id'] = (int)$vendor->getWebsiteId();
            $select->where('website_id = :website_id');
        }

        $vendorId = $adapter->fetchOne($select, $bind);
        if ($vendorId) {
            $this->load($vendor, $vendorId);
        } else {
            $vendor->setData(array());
        }

        return $this;
    }
	/**
     * Load vendor by vendor Id
     *
     * @throws Mage_Core_Exception
     *
     * @param VES_Vendors_Model_Vendor $vendor
     * @param string $vendorId
     * @param bool $testOnly
     * @return VES_Vendors_Model_Resource_Vendor
     */
    public function loadByVendorId(VES_Vendors_Model_Vendor $vendor, $vendorId, $testOnly = false)
    {
        $adapter = $this->_getReadAdapter();
        $bind    = array('vendor_id' => $vendorId);
        $select  = $adapter->select()
            ->from($this->getEntityTable(), array($this->getEntityIdField()))
            ->where('vendor_id = :vendor_id');
        if ($vendor->getSharingConfig()->isWebsiteScope()) {
            if (!$vendor->hasData('website_id')) {
                Mage::throwException(
                    Mage::helper('customer')->__('Vendor website ID must be specified when using the website scope')
                );
            }
            $bind['website_id'] = (int)$vendor->getWebsiteId();
            $select->where('website_id = :website_id');
        }

        $id = $adapter->fetchOne($select, $bind);
        if ($id) {
            $this->load($vendor, $id);
        } else {
            $vendor->setData(array());
        }

        return $this;
    }
    
	/**
     * Change reset password link token
     *
     * Stores new reset password link token and its creation time
     *
     * @param VES_Vendors_Model_Vendor $vendor
     * @param string $newResetPasswordLinkToken
     * @return Mage_Customer_Model_Resource_Customer
     */
    public function changeResetPasswordLinkToken(VES_Vendors_Model_Vendor $vendor, $newResetPasswordLinkToken) {
        if (is_string($newResetPasswordLinkToken) && !empty($newResetPasswordLinkToken)) {
            $vendor->setRpToken($newResetPasswordLinkToken);
            $currentDate = Varien_Date::now();
            $vendor->setRpTokenCreatedAt($currentDate);
            $this->saveAttribute($vendor, 'rp_token');
            $this->saveAttribute($vendor, 'rp_token_created_at');
        }
        return $this;
    }
}