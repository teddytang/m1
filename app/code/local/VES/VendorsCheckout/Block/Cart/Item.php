<?php
class VES_VendorsCheckout_Block_Cart_Item extends Mage_Checkout_Block_Cart
{
	/**
	 * Get items by each vendor
	 */
	public function getItemsByVendor(){
		if(!$this->getData('items_by_vendor')){
			$quote = Mage::getSingleton('checkout/session')->getQuote();
			$sortedItems = array();
			foreach($quote->getAllVisibleItems() as $item){
				$vendorId = $item->getProduct()->getVendorId();
				if(!isset($sortedItems[$vendorId])){
					$sortedItems[$vendorId] = array();
				}
				$sortedItems[$vendorId][] = $item;
			}
			$this->setData('items_by_vendor',$sortedItems);
		}
		return $this->getData('items_by_vendor');
	}
	
	/**
	 * Get number of items in cart
	 */
	public function getItemCount(){
		return $this->getQuote()->getItemsCount();
	}
	/**
     * Get item row html
     *
     * @param   Mage_Sales_Model_Quote_Item $item
     * @return  string
     */
    public function getItemHtml(Mage_Sales_Model_Quote_Item $item)
    {
        $renderer = $this->getParentBlock()->getItemRenderer($item->getProductType())->setItem($item);
        return $renderer->toHtml();
    }
    
    /**
	 * Get Vendor By Vendor Id
	 * @param Varien_Event_Observer $observer
	 */
    public function getVendor($vendorId){
    	return Mage::getModel('vendors/vendor')->load($vendorId);
    }
    /**
     * Get Process checkout Url By Vendor Id
     * @param string $vendorId
     */
    public function getProcessCheckoutUrl($vendorId){
    	return $this->getUrl('vendorscheckout/onepage/index',array('vendor'=>$vendorId));
    }
    
    /**
     * Get Clear cart Url by vendor id
     */
    public function getClearCartUrlByVendor($vendorId){
    	return $this->getUrl('vendorscheckout/cart/clear',array('vendor'=>$vendorId));
    }
}