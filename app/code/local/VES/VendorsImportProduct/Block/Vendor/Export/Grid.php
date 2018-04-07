<?php

class VES_VendorsImportProduct_Block_Vendor_Export_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
    {
        parent::__construct();
        $this->setId('exportBlockGrid');
        $this->_filterVisibility = false;
    }
	/**
     * Get current logged in vendor
     */
    public function getVendor(){
    	if(!$this->_vendor){
    		$this->_vendor = Mage::getSingleton('vendors/session')->getVendor();
    	}
    	return $this->_vendor;
    }
    
    protected function _prepareCollection()
    {
        $collection = new Varien_Data_Collection();
        /* @var $collection Mage_Cms_Model_Mysql4_Block_Collection */
    	$helper = Mage::helper('vendorsimport');
    	$vendor = $this->getVendor();
    	$dir 	= $helper->getVendorExportFolder($vendor);
	    if ($handle = opendir($dir)) {
		
	        while (false !== ($entry = readdir($handle))) {
		        if ($entry != "." && $entry != ".." && is_file($dir.$entry)) {
		            $file = array(
		            	'url'			=> $helper->getVendorImportFileUrl($entry,$vendor),
		            	'thumbnail_url'	=> $this->getSkinUrl('ves_vendors/importproduct/icons/file.png'),
		            	'file_name'		=> $entry,
		            	'file_size' 	=> Mage::getModel('directory/currency')->format(filesize($dir.$entry),array('display'=>Zend_Currency::NO_SYMBOL,precision=>0),false),
		            	'last_modified'	=> Mage::app()->getLocale()->date(filemtime($dir.$entry))->toString(Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM))
		            );
		            $collection->addItem(new Varien_Object($file));
		        }
		    }
		}
		
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $baseUrl = $this->getUrl();

        $this->addColumn('file_name', array(
            'header'    => Mage::helper('vendorsimport')->__('Filename'),
            'align'     => 'left',
            'index'     => 'file_name',
        	'sortable'      => false,
        ));

        $this->addColumn('file_size', array(
            'header'    => Mage::helper('vendorsimport')->__('Filesize (Byte)'),
            'align'     => 'left',
        	'width'		=> '100px',
        	'sortable'  => false,
            'index'     => 'file_size'
        ));
        $this->addColumn('last_modified', array(
            'header'    => Mage::helper('vendorsimport')->__('Last modified'),
            'align'     => 'left',
            'index'     => 'last_modified',
        	'sortable'      => false,
        	'width'		=> '100px',
            'type'		=> 'date',
        ));

        return parent::_prepareColumns();
    }

	protected function _prepareMassaction()
    {
        $this->setMassactionIdField('file_name');
        $this->getMassactionBlock()->setFormFieldName('filename');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('vendorsimport')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('vendorsimport')->__('Are you sure?')
        ));

        return $this;
    }
    /**
     * Row click url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return Mage::helper('vendorsimport')->getVendorExportFileUrl($row->getFileName(),$this->getVendor());
    }
}