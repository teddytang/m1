<?php

class VES_VendorsProduct_Model_Resource_Catalog_Product_Link_Product_Collection extends Mage_Catalog_Model_Resource_Product_Link_Product_Collection {
	/**
     * Retrieve is flat enabled flag
     * Return always false if magento run admin
     *
     * @return bool
     */
    public function isEnabledFlat()
    {
        // Flat Data can be used only on frontend
        if (Mage::app()->getRequest()->getModuleName()=='vendors') {
            return false;
        }
        
        return parent::isEnabledFlat();
    }
}