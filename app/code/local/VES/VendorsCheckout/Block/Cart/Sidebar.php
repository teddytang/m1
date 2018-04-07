<?php
class VES_VendorsCheckout_Block_Cart_Sidebar extends Mage_Checkout_Block_Cart_Sidebar
{    
	/**
     * Get shopping cart items qty based on configuration (summary qty or items qty)
     *
     * @return int | float
     */
    public function getSummaryCount()
    {
        /*Do nothing if the extension is disabled*/
        if(!Mage::helper('vendors')->moduleEnabled()) return parent::getSummaryCount();
        
    	/*Return if it's not advanced mode*/
    	if(Mage::helper('vendors')->getMode() != VES_Vendors_Model_Vendor::MODE_ADVANCED) return parent::getSummaryCount();
    	
        if (!$this->getData('summary_qty')) {
	        $quotes = Mage::getSingleton('vendorscheckout/session')->getQuotes();
			$count = 0;
			if($quotes && sizeof($quotes)) foreach($quotes as $quote){
				$count += $quote->getItemsQty();
			}
			$this->setData('summary_qty',$count);
        }
        
    	
        return $this->getData('summary_qty');
    }
    
	/**
     * Get shopping cart subtotal.
     *
     * It will include tax, if required by config settings.
     *
     * @param   bool $skipTax flag for getting price with tax or not. Ignored in case when we display just subtotal incl.tax
     * @return  decimal
     */
    public function getSubtotal($skipTax = true)
    {
        /*Do nothing if the extension is disabled*/
        if(!Mage::helper('vendors')->moduleEnabled()) return parent::getSubtotal();
        
    	/*Return if it's not advanced mode*/
    	if(Mage::helper('vendors')->getMode() != VES_Vendors_Model_Vendor::MODE_ADVANCED) return parent::getSubtotal($skipTax);
    	
    	$quotes = Mage::getSingleton('vendorscheckout/session')->getQuotes();
		$subtotal = 0;
		foreach($quotes as $quote){
			$totals = $quote->getTotals();
			$config = Mage::getSingleton('tax/config');
	        if (isset($totals['subtotal'])) {
	            if ($config->displayCartSubtotalBoth()) {
	                if ($skipTax) {
	                    $subtotal += $totals['subtotal']->getValueExclTax();
	                } else {
	                    $subtotal += $totals['subtotal']->getValueInclTax();
	                }
	            } elseif($config->displayCartSubtotalInclTax()) {
	                $subtotal += $totals['subtotal']->getValueInclTax();
	            } else {
	                $subtotal += $totals['subtotal']->getValue();
	                if (!$skipTax && isset($totals['tax'])) {
	                    $subtotal+= $totals['tax']->getValue();
	                }
	            }
	        }
		}
        
        return $subtotal;
    }
    
    
	/**
     * Return customer quote items
     *
     * @return array
     */
    public function getItems()
    {
        /*Do nothing if the extension is disabled*/
        if(!Mage::helper('vendors')->moduleEnabled()) return parent::getItems();
        
        
    	/*Return if it's not advanced mode*/
    	if(Mage::helper('vendors')->getMode() != VES_Vendors_Model_Vendor::MODE_ADVANCED) return parent::getItems();
    	
    	$quotes = Mage::getSingleton('vendorscheckout/session')->getQuotes();
		$items = array();
		foreach($quotes as $quote){
			$items = array_merge($items,$quote->getAllVisibleItems());
		}
		return $items;
    }
}