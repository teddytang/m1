<?php
class VES_VendorsProduct_Model_Category extends Mage_Catalog_Model_Category
{
	 /**
     * Initialize resource mode
     *
     * @return void
     */
    protected function _construct()
    {
        // If Flat Data enabled then use it but only on frontend
		// Flat Data can be used only on frontend
    
        $flatHelper = Mage::helper('catalog/category_flat');
        if ($flatHelper->isAvailable() && !Mage::app()->getStore()->isAdmin() && $flatHelper->isBuilt(true)
            && !$this->getDisableFlat() && Mage::app()->getRequest()->getModuleName()!='vendors'
        ) {
            $this->_init('catalog/category_flat');
            $this->_useFlatResource = true;
        } else {
            $this->_init('catalog/category');
        }
    }
}