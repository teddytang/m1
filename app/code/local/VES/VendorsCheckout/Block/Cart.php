<?php
class VES_VendorsCheckout_Block_Cart extends Mage_Checkout_Block_Cart
{
    protected $_block_render;
    
	protected function _getVendorCheckoutSession(){
		return Mage::getSingleton('vendorscheckout/session');
	}
	/**
	 * Replace the top cart link by the new one.
	 */
	protected function _prepareLayout(){
		return parent::_prepareLayout();
	}
	/**
	 * Get shopping cart item count
	 */
	public function getItemsCount(){
		$quotes = $this->getQuotes();
		$itemCount = 0;
		foreach($quotes as $quote){
			$itemCount += $quote->getItemsCount();
		}
		return $itemCount;
	}
	
	public function getQuotes(){
		$quotes = $this->_getVendorCheckoutSession()->getQuotes();
		//$quotes[] = Mage::getSingleton('checkout/session')->getQuote();
		return $quotes;
	}
	/**
	 * Add block render
	 * @param string $blockName
	 * @param string $blockType
	 * @param string $template
	 */
	public function addBlockRender($blockName, $blockType, $template){
	    if(!isset($this->_block_render)) $this->_block_render = array();
	    $tmp = array(
	        'type'     => $blockType,
	        'template' => $template,
	    );
	    $this->_block_render[$blockName] = new Varien_Object($tmp);
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
     * Get item row html
     *
     * @param   Mage_Sales_Model_Quote_Item $item
     * @return  string
     */
    public function getSubcartHtml($quote)
    {
        $blockInfo = $this->getBlockRender('subcart');
        $block = $this->getLayout()->createBlock($blockInfo->getType())
            ->setQuote($quote)
            ->setBlockRender($this->_block_render)
            ->setTemplate($blockInfo->getTemplate());
        return $block->toHtml();
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
