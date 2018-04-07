<?php
class VES_VendorsCredit_Credit_TransactionController extends VES_Vendors_Controller_Action
{
	public function indexAction(){
    	$this->loadLayout()
		->_setActiveMenu('sales')->_title($this->__('Credit Transactions'))
		->_addBreadcrumb(Mage::helper('vendorssales')->__('Sales'), Mage::helper('vendorssales')->__('Sales'))
    	->_addBreadcrumb(Mage::helper('vendorscredit')->__('Credit Transactions'), Mage::helper('vendorscredit')->__('Credit Transactions'));
		$this->renderLayout();
	}
	
	public function exportCsvAction()
	{
	    $fileName   = 'transaction.csv';
	    $content    = $this->getLayout()->createBlock('vendorscredit/transaction_grid')
	    ->getCsv();
	
	    $this->_sendUploadResponse($fileName, $content);
	}
	
	public function exportXmlAction()
	{
	    $fileName   = 'transaction.xml';
	    $content    = $this->getLayout()->createBlock('vendorscredit/transaction_grid')
	    ->getXml();
	
	    $this->_sendUploadResponse($fileName, $content);
	}
	
	protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
	{
	    $response = $this->getResponse();
	    $response->setHeader('HTTP/1.1 200 OK','');
	    $response->setHeader('Pragma', 'public', true);
	    $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
	    $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
	    $response->setHeader('Last-Modified', date('r'));
	    $response->setHeader('Accept-Ranges', 'bytes');
	    $response->setHeader('Content-Length', strlen($content));
	    $response->setHeader('Content-type', $contentType);
	    $response->setBody($content);
	    $response->sendResponse();
	    die;
	}
}