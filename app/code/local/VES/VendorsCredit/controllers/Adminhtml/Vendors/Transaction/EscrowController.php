<?php

class VES_VendorsCredit_Adminhtml_Vendors_Transaction_EscrowController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/vendors/transaction/escrow');
    }
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('vendors/vendors')
			->_addBreadcrumb(Mage::helper('vendorscredit')->__('Escrow Transactions'), Mage::helper('vendorscredit')->__('Escrow Transactions'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}
 
	public function viewAction() {
		try{
		    $escrow = Mage::getModel('vendorscredit/escrow')->load($this->getRequest()->getParam('id'));
    		if(!$escrow->getId()){
    		    throw new Mage_Core_Exception(Mage::helper('vendorscredit')->__('The transaction is not exist.'));
    		}
    		Mage::register('current_escrow', $escrow);
    		$this->loadLayout();
    		$this->_setActiveMenu('vendors/credit');
    		$this->_addBreadcrumb(Mage::helper('vendorscredit')->__('Escrow Transactions'), Mage::helper('vendorscredit')->__('Escrow Transactions'));
    		$this->_addBreadcrumb(Mage::helper('vendorscredit')->__('View Escrow Transaction'), Mage::helper('vendorscredit')->__('View Escrow Transaction'));
    		$this->renderLayout();
    		
		}catch (Exception $e){
		    Mage::getSingleton('adminhtml/session')->addError($e->getMessge());
		    $this->_redirect('*/*');
		}
	}
 
	public function cancelAction() {
	    try{
	        $escrow = Mage::getModel('vendorscredit/escrow')->load($this->getRequest()->getParam('id'));
	        if(!$escrow->getId()){
	            throw new Mage_Core_Exception(Mage::helper('vendorscredit')->__('The transaction is not exist.'));
	        }
	        $escrow->cancelPayment();
	        
	        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('vendorscredit')->__('The transaction has been canceled.'));
	    }catch (Exception $e){
	        Mage::getSingleton('adminhtml/session')->addError($e->getMessge());
	    }
        $this->_redirect('*/*/');
	}
	
	public function releaseAction() {
	    try{
	        $escrow = Mage::getModel('vendorscredit/escrow')->load($this->getRequest()->getParam('id'));
	        if(!$escrow->getId()){
	            throw new Mage_Core_Exception(Mage::helper('vendorscredit')->__('The transaction is not exist.'));
	        }
	        
	        $escrow->releasePayment();
	        
	        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('vendorscredit')->__('The transaction has been released.'));
	    }catch (Exception $e){
	        Mage::getSingleton('adminhtml/session')->addError($e->getMessge());
	    }
	    $this->_redirect('*/*/');
	}
}