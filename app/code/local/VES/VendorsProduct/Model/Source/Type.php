<?php
class VES_VendorsProduct_Model_Source_Type extends Mage_Catalog_Model_Product_Type
{
	public function getVendorOptionArray() {
 		$availableTypes = explode(",", Mage::getStoreConfig('vendors/catalog/product_type'));
 		$res = array();
        foreach (self::getOptionArray() as $index => $value) {
        	if(!in_array($index, $availableTypes)) continue;
            $res[$index] = $value;
        }
        return $res;
	}
	public function toOptionArray() {
		return $this->getOptions();
	}
}