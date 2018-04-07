<?php
class VES_VendorsSales_Sales_CreditmemoController extends VES_Vendors_Controller_Action
{
	/**
     * Orders grid
     */
	public function indexAction(){
		$this->loadLayout();
		$this->_setActiveMenu('sales')->_title($this->__('Sales'))->_title($this->__('Credit Memos'));
    	$this->_addBreadcrumb($this->__('Sales'), $this->__('Sales'));
    	$this->_addBreadcrumb($this->__('Credit Memos'), $this->__('Credit Memos'));
		$this->renderLayout();
	}
	
	/**
     * Order grid
     */
    public function gridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }
    
    /**
     * Creditmemo information page
     */
    public function viewAction()
    {
    	if ($creditmemoId = $this->getRequest()->getParam('creditmemo_id')) {
    		$this->_forward('view', 'sales_order_creditmemo', null, array('come_from' => 'sales_creditmemo'));
    	} else {
    		$this->_forward('noRoute');
    	}
    }
    
	/**
     * Export credit memo grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName   = 'creditmemos.csv';
        $grid       = $this->getLayout()->createBlock('vendorssales/vendor_sales_creditmemo_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     *  Export credit memo grid to Excel XML format
     */
    public function exportExcelAction()
    {
        $fileName   = 'creditmemos.xml';
        $grid       = $this->getLayout()->createBlock('vendorssales/vendor_sales_creditmemo_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
    
	public function pdfcreditmemosAction(){
        $creditmemosIds = $this->getRequest()->getPost('creditmemo_ids');
        $creditmemosIds = explode(",", $creditmemosIds);
        if (!empty($creditmemosIds)) {
            $invoices = Mage::getResourceModel('sales/order_creditmemo_collection')
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', array('in' => $creditmemosIds))
                ->load();
            if (!isset($pdf)){
                $pdf = Mage::getModel('sales/order_pdf_creditmemo')->getPdf($invoices);
            } else {
                $pages = Mage::getModel('sales/order_pdf_creditmemo')->getPdf($invoices);
                $pdf->pages = array_merge ($pdf->pages, $pages->pages);
            }

            return $this->_prepareDownloadResponse('creditmemo'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').
                '.pdf', $pdf->render(), 'application/pdf');
        }
        $this->_redirect('*/*/');
    }
}