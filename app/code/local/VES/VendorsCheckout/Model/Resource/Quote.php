<?php

class VES_VendorsCheckout_Model_Resource_Quote extends Mage_Sales_Model_Resource_Quote{
    /**
     * Load quote data by customer identifier and vendor id
     *
     * @param Mage_Sales_Model_Quote $quote
     * @param int $customerId
     * * @param int $vendorId
     * @return Mage_Sales_Model_Resource_Quote
     */
    public function loadQuoteByCustomerId($quote, $customerId,$vendorId)
    {
        $adapter = $this->_getReadAdapter();
        $select  = $this->_getLoadSelect('customer_id', $customerId, $quote)
        	->where('vendor_id=?', $vendorId)
            ->where('is_active = ?', 1)
            ->order('updated_at ' . Varien_Db_Select::SQL_DESC)
            ->limit(1);

        $data    = $adapter->fetchRow($select);

        if ($data) {
            $quote->setData($data);
        }

        $this->_afterLoad($quote);

        return $this;
    }
    
	/**
     * Load quote data by customer identifier
     *
     * @param Mage_Sales_Model_Quote $quote
     * @param int $customerId
     * @return Mage_Sales_Model_Resource_Quote
     */
    public function loadByCustomerId($quote, $customerId)
    {
        $adapter = $this->_getReadAdapter();
        $select  = $this->_getLoadSelect('customer_id', $customerId, $quote)
            ->where('is_active = ?', 1)
            ->where('vendor_id > ?', 0)
            ->order('updated_at ' . Varien_Db_Select::SQL_DESC)
            ->limit(1);

        $data    = $adapter->fetchRow($select);

        if ($data) {
            $quote->setData($data);
        }

        $this->_afterLoad($quote);

        return $this;
    }
}
