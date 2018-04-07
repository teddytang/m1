<?php

class VES_VendorsImportProduct_Block_Vendor_Export_Edit extends Mage_Adminhtml_Block_Widget_Form_Container{
	public function __construct(){
		parent::__construct();
		$this->_headerText = Mage::helper('vendorsimport')->__('Export Products');
		$this->_controller = 'vendor_export';
		$this->_blockGroup = 'vendorsimport';
		$this->updateButton('save', 'label',Mage::helper('vendorsimport')->__('Export'));
		$this->updateButton('save', 'onclick','vendorExportProducts()');
		$this->removeButton('reset');
		
		$this->_formScripts[] = '
			$("export_status").up(1).hide();
			function vendorExportProducts(){
				if(editForm.validate()){
					if(!$("export_file_name").value) {alert("'.Mage::helper('vendorsimport')->__('Please enter the file name.').'");return;}
					
					$("export_status").up(1).show();
					$("export_status_iframe").src = "'.$this->getUrl('vendors/export_index/run').'id/"+$("export_type").value +"/filename/"+$("export_file_name").value;
				}
			}
		';
	}
}