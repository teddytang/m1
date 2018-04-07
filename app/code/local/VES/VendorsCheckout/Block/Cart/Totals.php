<?php

class VES_VendorsCheckout_Block_Cart_Totals extends Mage_Checkout_Block_Cart_Totals
{
	public function setQuote($quote){ 
		$this->_quote = $quote;
		return $this;
	}

	protected function _getTotalRenderer($code)
	{
	    $blockName = $this->getQuote()->getId().$code.'_total_renderer';
	    $block = $this->getLayout()->getBlock($blockName);
	    if (!$block) {
	        $block = $this->_defaultRenderer;
	        $config = Mage::getConfig()->getNode("global/sales/quote/totals/{$code}/renderer");
	        if ($config) {
	            $block = (string) $config;
	        }
	
	        $block = $this->getLayout()->createBlock($block, $blockName);
	        $block->setQuote($this->_quote);
	    }
	    /**
	     * Transfer totals to renderer
	     */
	    $block->setTotals($this->getTotals());
	    return $block;
	}
}
