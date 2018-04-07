<?php
class VES_VendorsImportProduct_Block_Adminhtml_System_Convert_Marketplace_Gui extends Mage_Adminhtml_Block_System_Convert_Gui
{
	public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'vendorsimport';
        $this->_controller = 'adminhtml_system_convert_marketplace_gui';
    }
}