<?php

class VES_VendorsImportProduct_Block_Adminhtml_System_Convert_Gui_Edit_Tab_Wizard extends Mage_Adminhtml_Block_System_Convert_Gui_Edit_Tab_Wizard
{
	public function __construct()
    {
        parent::__construct();
        $this->setTemplate('ves_vendorsimportproduct/system/convert/profile/wizard.phtml');
    }
}
