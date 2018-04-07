<?php
class VES_VendorsProduct_Model_Source_Attributeset
{
	protected function _getCollection(){
		$entityTypeId = Mage::getModel('eav/entity_type')->getCollection()->addFieldToFilter('entity_type_code','catalog_product')->getFirstItem()->getId();
 		 
        $collection = Mage::getModel('eav/entity_attribute_set')->getCollection()->addFieldToSelect('*')->addFieldToFilter('entity_type_id',array('eq'=>$entityTypeId));
        return $collection;
	}
	
	public function toOptionArray() {
 		 $data=array();
 		 $collection = $this->_getCollection();
         foreach($collection as $attributeSet){
         	$data[] = array(
         			'label' => $attributeSet->getData('attribute_set_name'),
         			'value' => $attributeSet->getData('attribute_set_id')
         	);
         }
         return $data;
	} 
	
	public function getOptionArray() {
 		 $data=array();
 		 $collection = $this->_getCollection();
         foreach($collection as $attributeSet){
         	$data[$attributeSet->getData('attribute_set_id')] = $attributeSet->getData('attribute_set_name');
         }
         return $data;
	}
	
	public function getVendorOptionArray() {
 		 $data=array();
 		 $availableAttribuetSet = Mage::getStoreConfig('vendors/catalog/attribute_sets');
 		 $collection = $this->_getCollection()->addFieldToFilter('attribute_set_id',array('in'=>explode(',',$availableAttribuetSet)));
         foreach($collection as $attributeSet){
         	$data[$attributeSet->getData('attribute_set_id')] = $attributeSet->getData('attribute_set_name');
         }
         return $data;
	}
}