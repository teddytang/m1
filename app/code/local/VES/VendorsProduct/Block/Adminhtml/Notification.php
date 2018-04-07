<?php

/**
 * Catalog manage products block
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <core@magentocommerce.com>
 */
class VES_VendorsProduct_Block_Adminhtml_Notification extends Mage_Adminhtml_Block_Template
{
    protected $_collection;
    /**
     * Get pending product collection
     */
	public function getPendingProductCollection(){
	    if(!$this->_collection){
    	    $this->_collection = Mage::getModel('catalog/product')->getCollection()
    	    ->addAttributeToFilter('vendor_id',array('gt'=>0))
    	    ->addAttributeToFilter('approval',VES_VendorsProduct_Model_Source_Approval::STATUS_PENDING);
			
			//check vendors were deleted or not
			$this->_collection->joinTable(array('vendor_table'=>'vendors/vendor'),'entity_id = vendor_id',array('vendor'=>'vendor_id'));

	    }
	    
	    return $this->_collection;
	}
	/**
	 * Get number of pending product
	 */
	public function getPendingProductCount(){
	    return $this->getPendingProductCollection()->count();
	}
	
	
	public function getMessage(){
	    $productCount = $this->getPendingProductCount();
	    if($productCount == 1)return $this->__('There is %s product awaiting for approval. <a href="%s">Click Here</a> to review them.','<strong>'.$productCount.'</strong>',$this->getUrl('adminhtml/vendors_catalog_product/pending'));
	    return $this->__('There are %s products awaiting for approval. <a href="%s">Click Here</a> to review them.','<strong>'.$productCount.'</strong>',$this->getUrl('adminhtml/vendors_catalog_product/pending'));
	}
	public function _toHtml(){
	    if(!$this->getPendingProductCount()) return '';
	    return parent::_toHtml();
	}
}
