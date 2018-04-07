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
		$hasProductJoinEntityTbl = false;

		foreach($froms as $from){
			if(isset($from['tableName']) && ($from['tableName'] == $collection->getTable('catalog/product'))){
				if($from['joinType'] == "from")
						$hasProductEntityTbl = true;
				else
					$hasProductJoinEntityTbl = true;
			} 

			if(isset($from['tableName']) && ($from['tableName'] == $collection->getTable('catalog/product_index_price'))){
				$hasProductPriceEntityTbl = true;
			} 
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
			//var_dump($ids);exit;
			if (!$hasProductEntityTbl) {
				if(sizeof($ids)) $collection->getSelect()->where('vendor_id NOT IN (?)',$ids);
			}else{
				if($hasProductPriceEntityTbl){
					if(!$hasProductJoinEntityTbl) {
						$resource = Mage::getSingleton('core/resource');
						$ticketTable = $resource->getTableName('catalog/product');
	           			$collection->getSelect()->joinLeft(array("catalog" => $ticketTable), "e.entity_id = catalog.entity_id");
						if(sizeof($ids)) $collection->getSelect()->where('catalog.vendor_id NOT IN (?)',$ids);
					}else{
						if(sizeof($ids)) $collection->getSelect()->where('vendor_id NOT IN (?)',$ids);
					}

					//echo $collection->getSelect();exit;
				} 
				/*Fix for price filter*/
				else if(sizeof($ids)) $collection->addFieldToFilter('vendor_id', array('nin' => $ids));
			}
			$this->setIsFilteredActivatedVendors(true);
		}

		Mage::dispatchEvent('ves_vendorsproduct_product_list_prepare_after',array('collection'=>$collection));
		
		return $collection;
	}
}
