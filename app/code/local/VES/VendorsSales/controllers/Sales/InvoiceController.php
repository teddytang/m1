<?php
class VES_VendorsSales_Sales_InvoiceController extends VES_Vendors_Controller_Action
{
	/**
     * Orders grid
     */
	public function indexAction(){
		if(!Mage::getStoreConfig('vendors/sales/view_invoices')){
    		$this->_forward('no-route');
    		return;
    	}
		$this->loadLayout();
		$this->_setActiveMenu('sales')->_title($this->__('Sales'))->_title($this->__('Invoices'));
    	$this->_addBreadcrumb($this->__('Sales'), $this->__('Sales'));
    	$this->_addBreadcrumb($this->__('Invoices'), $this->__('Invoices'));
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
     * Invoice information page
     */
    public function viewAction()
    {
        if ($invoiceId = $this->getRequest()->getParam('invoice_id')) {
            $this->_forward('view', 'sales_order_invoice', null, array('come_from'=>'invoice'));
        } else {
            $this->_forward('noRoute');
        }
    }
    


    
    /**
     * Initialize invoice model instance
     *
     * @return Mage_Sales_Model_Order_Invoice
     */
    protected function _initInvoice($update = false)
    {
    	$this->_title($this->__('Sales'))->_title($this->__('Invoices'));
    
    	$invoice = false;
    	$itemsToInvoice = 0;
    	$invoiceId = $this->getRequest()->getParam('invoice_id');
    	$orderId = $this->getRequest()->getParam('order_id');
    	if ($invoiceId) {
    		$invoice = Mage::getModel('sales/order_invoice')->load($invoiceId);
    		if (!$invoice->getId()) {
    			$this->_getSession()->addError($this->__('The invoice no longer exists.'));
    			return false;
    		}
    	} elseif ($orderId) {
    		$order = Mage::getModel('sales/order')->load($orderId);
    		/**
    		 * Check order existing
    		*/
    		if (!$order->getId()) {
    			$this->_getSession()->addError($this->__('The order no longer exists.'));
    			return false;
    		}
    		/**
    		 * Check invoice create availability
    		 */
    		if (!$order->canInvoice()) {
    			$this->_getSession()->addError($this->__('The order does not allow creating an invoice.'));
    			return false;
    		}
    		$savedQtys = $this->_getItemQtys();
    		$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice($savedQtys);
    		if (!$invoice->getTotalQty()) {
    			Mage::throwException($this->__('Cannot create an invoice without products.'));
    		}
    	}
    
    	Mage::register('current_invoice', $invoice);
    	return $invoice;
    }
    
	/**
     * Export invoice grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName   = 'invoices.csv';
        $grid       = $this->getLayout()->createBlock('vendorssales/vendor_sales_invoice_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     *  Export invoice grid to Excel XML format
     */
    public function exportExcelAction()
    {
        $fileName   = 'invoices.xml';
        $grid       = $this->getLayout()->createBlock('vendorssales/vendor_sales_invoice_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
    
	public function pdfinvoicesAction(){
        $invoicesIds = $this->getRequest()->getPost('invoice_ids');
        $invoicesIds = explode(",", $invoicesIds);
        if (!empty($invoicesIds)) {
            $invoices = Mage::getResourceModel('sales/order_invoice_collection')
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', array('in' => $invoicesIds))
                ->load();
            if (!isset($pdf)){
                $pdf = Mage::getModel('sales/order_pdf_invoice')->getPdf($invoices);
            } else {
                $pages = Mage::getModel('sales/order_pdf_invoice')->getPdf($invoices);
                $pdf->pages = array_merge ($pdf->pages, $pages->pages);
            }

            return $this->_prepareDownloadResponse('invoice'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').
                '.pdf', $pdf->render(), 'application/pdf');
        }
        $this->_redirect('*/*/');
    }
    
}