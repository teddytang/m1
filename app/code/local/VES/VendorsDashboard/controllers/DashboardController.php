<?php
class VES_VendorsDashboard_DashboardController extends VES_Vendors_Controller_Action
{
    /**
     * Default vendor account page
     */
	public function indexAction()
    {
    	$this->loadLayout()->_setActiveMenu('dashboard')->_title($this->__('DashBoard'));
    	$this->_addBreadcrumb(Mage::helper('vendorsdashboard')->__('DashBoard'), Mage::helper('vendorsdashboard')->__('DashBoard'));
		$this->renderLayout();
    }
    
	public function tunnelAction()
    {
        $httpClient = new Varien_Http_Client();
        $gaData = $this->getRequest()->getParam('ga');
        $gaHash = $this->getRequest()->getParam('h');
        if ($gaData && $gaHash) {
            $newHash = Mage::helper('adminhtml/dashboard_data')->getChartDataHash($gaData);
            if ($newHash == $gaHash) {
                if ($params = unserialize(base64_decode(urldecode($gaData)))) {
                    $response = $httpClient->setUri(Mage_Adminhtml_Block_Dashboard_Graph::API_URL)
                            ->setParameterGet($params)
                            ->setConfig(array('timeout' => 5))
                            ->request('GET');

                    $headers = $response->getHeaders();

                    $this->getResponse()
                        ->setHeader('Content-type', $headers['Content-type'])
                        ->setBody($response->getBody());
                }
            }
        }
    }
    
	public function ajaxBlockAction()
    {
        $output   = '';
        $blockTab = $this->getRequest()->getParam('block');
        $this->loadLayout();
        if (in_array($blockTab, array('tab_orders', 'tab_amounts', 'totals'))) {
        	$block = $this->getLayout()->createBlock('vendorsdashboard/vendor_dashboard_' . $blockTab);
            $output = $block->toHtml();
        }
        $this->getResponse()->setBody($output);
    }
}