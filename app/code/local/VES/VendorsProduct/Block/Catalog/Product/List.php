<?php

/**
 * Catalog product list
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsProduct_Block_Catalog_Product_List extends Mage_Catalog_Block_Product_List
{
	protected function _getProductCollection(){
	    /*Do nothing if the extension is disabled*/
	    if(!Mage::helper('vendors')->moduleEnabled()) return parent::_getProductCollection();
		$collection = parent::_getProductCollection();
		$froms = $collection->getSelect()->getPart('from');
		$hasProductEntityTbl = false;
		$hasProductPriceEntityTbl = false;
		foreach($froms as $from){
			if(isset($from['tableName']) && ($from['tableName'] == $collection->getTable('catalog/product')) && $from['joinType'] == "from") $hasProductEntityTbl = true;
			if(isset($from['tableName']) && ($from['tableName'] == $collection->getTable('catalog/product_index_price'))) $hasProductPriceEntityTbl = true;
		}
		if (!$hasProductEntityTbl) {
			$collection->getSelect()->where('approval = ?',VES_VendorsProduct_Model_Source_Approval::STATUS_APPROVED);
		}
		else{
			$collection->addAttributeToFilter('approval',VES_VendorsProduct_Model_Source_Approval::STATUS_APPROVED);
		}
		if(!$this->getIsFilteredActivatedVendors()){
			/*Get not activated vendors*/
			$vendorCollection = Mage::getModel('vendors/vendor')->getCollection()->addAttributeToFilter('status',array('neq'=>VES_Vendors_Model_Vendor::STATUS_ACTIVATED));
			$ids = $vendorCollection->getAllIds();
			$flag = new Varien_Object(array('vendor_ids'=>$ids));
      		Mage::dispatchEvent('ves_vendorsproduct_product_list_prepare_before',array('vendors'=>$flag));
      		$ids  = $flag->getVendorIds();
      		
			if (!$hasProductEntityTbl) {
				if(sizeof($ids)) $collection->getSelect()->where('vendor_id NOT IN (?)',$ids);
			}else{
				if($hasProductPriceEntityTbl) if(sizeof($ids)) $collection->getSelect()->where('vendor_id NOT IN (?)',$ids);
				/*Fix for price filter*/
				else if(sizeof($ids)) $collection->addFieldToFilter('vendor_id', array('nin' => $ids));
			}
			$this->setIsFilteredActivatedVendors(true);
		}

		Mage::dispatchEvent('ves_vendorsproduct_product_list_prepare_after',array('collection'=>$collection));
		
		return $collection;
	}
}
