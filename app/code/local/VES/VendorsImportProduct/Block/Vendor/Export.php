<?php
class VES_VendorsImportProduct_Block_Vendor_Export extends Mage_Adminhtml_Block_Widget_Grid_Container{
	public function __construct(){
		$this->_controller = 'vendor_export';
		$this->_blockGroup = 'vendorsimport';
		$this->_headerText = $this->getTitle();
		$this->_addButtonLabel = Mage::helper('vendorsimport')->__('Export Products');
		parent::__construct();
	}
	
	/**
	 * Get Title
	 */
	public function getTitle(){
		return Mage::helper('vendorsimport')->__('Export Files');
	}
	
	public function getCreateUrl()
    {
        return $this->getUrl('*/*/export');
    }
}