<?php
class VES_Vendors_Block_Page_Head extends Mage_Core_Block_Template
{
	protected function _prepareLayout(){
		parent::_prepareLayout();
		$headBlock = $this->getLayout()->getBlock('head');
		
		if($headBlock){
			$headBlock->addItem('link_rel', Mage::getUrl("vendors/css/index"),'rel="stylesheet" type="text/css"');
			$headBlock->setTitle(Mage::getStoreConfig('vendors/config/head_default_title'));
		}

	}

}