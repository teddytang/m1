<?php

/**
 * Vendor dashboard diagram tabs
 *
 * @category   	VES
 * @package    	VES_Vendors
 * @author    	Vnecoms Team <support@vnecoms.com>
 */

class VES_VendorsDashboard_Block_Vendor_Dashboard_Diagrams extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('diagram_tab');
        $this->setDestElementId('diagram_tab_content');
        $this->setTemplate('widget/tabshoriz.phtml');
    }

    protected function _prepareLayout()
    {
    	if(Mage::helper('vendors')->isAdvancedMode()){
    		$this->addTab('orders', array(
	            'label'     => $this->__('Orders'),
	            'content'   => $this->getLayout()->createBlock('vendorsdashboard/vendor_dashboard_tab_orders')->toHtml(),
	            'active'    => true
	        ));
	
	        $this->addTab('amounts', array(
	            'label'     => $this->__('Amounts'),
	            'content'   => $this->getLayout()->createBlock('vendorsdashboard/vendor_dashboard_tab_amounts')->toHtml(),
	        ));
    	}else{
    		/*$this->addTab('orders', array(
	            'label'     => $this->__('Items'),
	            'content'   => $this->getLayout()->createBlock('vendorsdashboard/vendor_dashboard_general_tab_items')->toHtml(),
	            'active'    => true
	        ));*/
    	}
        return parent::_prepareLayout();
    }
}
