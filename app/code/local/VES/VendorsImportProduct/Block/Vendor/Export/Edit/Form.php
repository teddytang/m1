<?php

class VES_VendorsImportProduct_Block_Vendor_Export_Edit_Form extends Mage_Adminhtml_Block_Widget_Form{
	public function __construct()
    {
        parent::__construct();
        $this->setId('export_form');
        $this->setTitle(Mage::helper('vendorsimport')->__('Export products'));
    }
    
	protected function _prepareForm(){
		$form = new Varien_Data_Form(
            array('id' => 'edit_form', 'action' => $this->getUrl('*/*/process'), 'method' => 'post')
        );
		
        $form->setHtmlIdPrefix('export_');
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('vendorsimport')->__('Export products'), 'class' => 'fieldset-wide'));
        $fieldset->addField('type', 'select', array(
            'label'     => Mage::helper('vendorsimport')->__('Type'),
            'title'     => Mage::helper('vendorsimport')->__('Type'),
            'name'      => 'is_active',
            'required'  => true,
            'options'   => Mage::getModel('vendorsimport/source_export')->getOptionArray(),
        ));
        
        $fieldset->addField('file_name', 'text', array(
            'name'      => 'title',
            'label'     => Mage::helper('vendorsimport')->__('Filename'),
            'title'     => Mage::helper('vendorsimport')->__('Filename'),
        	'class'		=> 'validate-code',
            'required'  => true,
        ));
        
        $fieldset->addField('status', 'note', array(
            'name'      => 'export_status',
        	'class'		=> 'validate-code',
            'required'  => true,
        	'text'		=> '<iframe src="" id="export_status_iframe" style="border: 1px solid #d3d3d3; width: 99%; height: 400px;"></iframe>',
        ));
        
        $form->setUseContainer(true);
        $this->setForm($form);
        

        return parent::_prepareForm();
	}
}