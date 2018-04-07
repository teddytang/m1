<?php

class VES_VendorsProduct_Block_Store_Switcher extends Mage_Adminhtml_Block_Store_Switcher
{
	protected $_hasDefaultOption = true;
	
	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('ves_vendorsproduct/switcher.phtml');
	}
	
	/**
	 * Get websites
	 *
	 * @return array
	 */
	public function getWebsiteVendor()
	{
		$websiteId = Mage::app()->getWebsite()->getId();
		$website = Mage::getMOdel("core/website")->load($websiteId);
		return $website;
	}
}
