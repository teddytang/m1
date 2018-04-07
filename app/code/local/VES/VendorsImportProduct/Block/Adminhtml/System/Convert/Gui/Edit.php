<?php

class VES_VendorsImportProduct_Block_Adminhtml_System_Convert_Gui_Edit extends Mage_Adminhtml_Block_System_Convert_Gui_Edit
{
	public function __construct()
    {
        parent::__construct();
        $this->_controller = 'adminhtml_system_convert_gui';
		$this->_blockGroup = 'vendorsimport';
    }
}
