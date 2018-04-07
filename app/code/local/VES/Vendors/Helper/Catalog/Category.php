<?php

class VES_Vendors_Helper_Catalog_Category extends Mage_Catalog_Helper_Category
{
	/**
     * Check if a category can be shown
     *
     * @param  Mage_Catalog_Model_Category|int $category
     * @return boolean
     */
    public function canShow($category)
    {
        if (is_int($category)) {
            $category = Mage::getModel('catalog/category')->load($category);
        }

        if (!$category->getId()) {
            return false;
        }

        if (!$category->getIsActive()) {
            return false;
        }
/*        if (!$category->isInRootCategoryList()) {
            return false;
        }*/

        return true;
    }
}