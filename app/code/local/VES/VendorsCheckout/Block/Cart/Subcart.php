<?php
class VES_VendorsCheckout_Block_Cart_Subcart extends Mage_Checkout_Block_Cart
{
	protected $_vendor;
	protected $_block_render;
	
	/**
	 * Set block render
	 * @param array $blockRender
	 */
	public function setBlockRender($blockRender = array()){
	    $this->_block_render = $blockRender;
	    return $this;
	}
	

	
	/**
	 * Get Block Render
	 * @param string $blockName
	 * @throws Mage_Core_Exception
	 */
	public function getBlockRender($blockName){
	    if(!isset($this->_block_render[$blockName])) throw new Mage_Core_Exception($this->__('The block %s does not exist!',$blockName));
	    return $this->_block_render[$blockName];
	}
	
	/**
	 * Set quote
	 * @param Mage_Sales_Model_Quote $quote
	 * @return VES_VendorsCheckout_Block_Cart_Subcart
	 */
	public function setQuote(Mage_Sales_Model_Quote $quote){
		$this->_quote = $quote;
		return $this;
	}
	/**
	 * Get vendor account
	 */
	public function getVendor(){
		if(!isset($this->_vendor)){
			$vendorId = $this->getQuote()->getVendorId();
			$this->_vendor = Mage::getModel('vendors/vendor')->load($vendorId);
		}
		return $this->_vendor;
	}
	
    /**
     * Return list of available checkout methods
     *
     * @param string $nameInLayout Container block alias in layout
     * @return array
     */
    public function getMethods($nameInLayout)
    {
    	$methods = array();
    	$method = $this->getLayout()->createBlock('vendorscheckout/onepage_link')->setQuote($this->getQuote())->setTemplate('checkout/onepage/link.phtml');
    	$methods[] = $method;
    	$methodsObj = new Varien_Object(array('methods'=>$methods));
    	
    	Mage::dispatchEvent('vendorscheckout_onepage_link',array('methods'=>$methodsObj));
    	return $methodsObj->getMethods();
    }

    public function getIsVirtual()
    {
        return $this->getQuote()->isVirtual();
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
        $renderer = $this->getLayout()->getBlock('checkout.cart')->getItemRenderer($item->getProductType())->setItem($item);
        return $renderer->toHtml();
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
    /**
     * Create child block by name
     * @param string $blockName
     */
    public function createChildBlock($blockName){
        $blockInfo = $this->getBlockRender($blockName);
        $block = $this->getLayout()->createBlock($blockInfo->getType())->setQuote($this->getQuote())->setTemplate($blockInfo->getTemplate());
        return $block;
    }
    /**
     * Get total block html
     */
    public function getTotalsBlockHtml(){
        $block = $this->createChildBlock('cart_totals');
    	return $block->toHtml();
    }
    
    /**
     * Get coupon block html
     */
    public function getCouponHtml(){
        $block = $this->createChildBlock('cart_coupon');
        return $block->toHtml();
    }
    
    /**
     * Get shipping block html
     */
    public function getShippingHtml(){
        $block = $this->createChildBlock('cart_shipping');
        return $block->toHtml();
    }
    /**
     * Disable the subcart if it has no item.
     * @see Mage_Core_Block_Template::_toHtml()
     */
    protected function _toHtml(){
    	if(!$this->getQuote()->getItemsCount()) return '';
    	return parent::_toHtml();
    }
    /**
     * Get update url
     */
    public function getUpdateUrl(){
    	if($vendorId = $this->getQuote()->getVendorId()) return $this->getUrl('checkout/cart/updatePost',array('vendor_id'=>$vendorId));
    	return $this->getUrl('checkout/cart/updatePost');
    }
}