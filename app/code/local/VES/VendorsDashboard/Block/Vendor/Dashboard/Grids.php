<?php
/**
 * Vendor dashboard bottom tabs
 *
 * @category   	VES
 * @package    	VES_Vendors
 * @author    	Vnecoms Team <support@vnecoms.com>
 */

class VES_VendorsDashboard_Block_Vendor_Dashboard_Grids extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('grid_tab');
        $this->setDestElementId('grid_tab_content');
        $this->setTemplate('widget/tabshoriz.phtml');
    }

    /**
     * Prepare layout for dashboard bottom tabs
     *
     * To load block statically:
     *     1) content must be generated
     *     2) url should not be specified
     *     3) class should not be 'ajax'
     * To load with ajax:
     *     1) do not load content
     *     2) specify url (BE CAREFUL)
     *     3) specify class 'ajax'
     *
     * @return Mage_Adminhtml_Block_Dashboard_Grids
     */
    protected function _prepareLayout()
    {
        // load this active tab statically
        /*$this->addTab('ordered_products', array(
            'label'     => $this->__('Bestsellers'),
            'content'   => $this->getLayout()->createBlock('vendorsdashboard/vendor_dashboard_tab_products_ordered')->toHtml(),
            'active'    => true
        ));*/
    	Mage::dispatchEvent('vendor_dashboard_grids_preparelayout',array('grids'=>$this));
        return parent::_prepareLayout();
    }
}
