<?php

class VES_VendorsCredit_Block_Adminhtml_Vendor_Grid extends Mage_Adminhtml_Block_Template{
	protected function _prepareLayout(){
		parent::_prepareLayout();
		$vendorGridContainer = $this->getLayout()->getBlock('vendors');
		if($vendorGridContainer){
			$grid = $vendorGridContainer->getChild('grid');
			$baseCurrencyCode = Mage::app()->getStore((int)$this->getParam('store'))->getBaseCurrencyCode();
			$grid->addColumnAfter('credit', array(
				'header'    => Mage::helper('vendorscredit')->__('Credit'),
				'align'     =>'right',
				'width'     => '100px',
				'index'     => 'credit',
				'type'      => 'currency',
				'currency_code'  => $baseCurrencyCode,
				'renderer'  => new VES_VendorsCredit_Block_Widget_Grid_Column_Renderer_Credit(),
			),'email');
		}
		return $this;
	}
}