<?php

/**
 * Vendor dashboard block
 *
 * @category   	VES
 * @package    	VES_Vendors
 * @author    	Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsDashboard_Block_Vendor_Dashboard extends Mage_Adminhtml_Block_Dashboard
{
	protected function _prepareLayout()
    {
    	if(Mage::helper('vendors')->isAdvancedMode()){
	        $this->setChild('lastOrders',
	                $this->getLayout()->createBlock('vendorsdashboard/vendor_dashboard_orders_grid')
	        );
	        $this->setChild('sales',
                $this->getLayout()->createBlock('vendorsdashboard/vendor_dashboard_sales')
       		);
       		$this->setChild('totals',
	        	$this->getLayout()->createBlock('vendorsdashboard/vendor_dashboard_totals')
	        );
    	}else{
    		$this->setChild('lastOrders',
	                $this->getLayout()->createBlock('vendorsdashboard/vendor_dashboard_general_items_grid')
	        );
	        $this->setChild('sales',
                $this->getLayout()->createBlock('vendorsdashboard/vendor_dashboard_general_sales')
       		);
    	}


        if (Mage::getStoreConfig(self::XML_PATH_ENABLE_CHARTS)) {
            $block = $this->getLayout()->createBlock('vendorsdashboard/vendor_dashboard_diagrams');
        } else {
            $block = $this->getLayout()->createBlock('adminhtml/template')
                ->setTemplate('dashboard/graph/disabled.phtml')
                ->setConfigUrl($this->getUrl('adminhtml/system_config/edit', array('section'=>'admin')));
        }
        $this->setChild('diagrams', $block);

        $this->setChild('grids',
                $this->getLayout()->createBlock('vendorsdashboard/vendor_dashboard_grids')
        );

        Mage_Adminhtml_Block_Template::_prepareLayout();
    }
}
