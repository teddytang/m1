<?php

class VES_VendorsCredit_Adminhtml_Vendors_TransactionController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/vendors/transaction/credit');
    }
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('vendors/vendors')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}
 
	public function newAction() {
		$this->loadLayout();
		$this->_setActiveMenu('vendors/credit');

		$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

		$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);


		$this->renderLayout();
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			$type = Mage::getModel('vendorscredit/type');		
			$data['net_amount']	= $data['amount'];
			try {
				$type->process($data);		
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('vendorscredit')->__('Transaction has been submited.'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/new');
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('vendorscredit')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
	public function exportCsvAction()
	{
	    $fileName   = 'transaction.csv';
	    $content    = $this->getLayout()->createBlock('vendorscredit/adminhtml_vendor_transaction_grid')
	    ->getCsv();
	
	    $this->_sendUploadResponse($fileName, $content);
	}
	
	public function exportXmlAction()
	{
	    $fileName   = 'transaction.xml';
	    $content    = $this->getLayout()->createBlock('vendorscredit/adminhtml_vendor_transaction_grid')
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