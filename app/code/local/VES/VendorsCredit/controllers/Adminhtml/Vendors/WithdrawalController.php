<?php

class VES_VendorsCredit_Adminhtml_Vendors_WithdrawalController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/vendors/withdrawal/withdrawal_requests');
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

	public function viewAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('vendorscredit/withdrawal')->load($id);

		if ($model->getId()) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('withdrawal_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('vendors/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);


			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('vendorscredit')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}

	public function saveAction() {
		if ($withdrawalId = $this->getRequest()->getParam('id')) {
			$model = Mage::getModel('vendorscredit/withdrawal')->load($withdrawalId);		
			
			$model->setNote($this->getRequest()->getPost('note'));
			try {
				if($this->getRequest()->getParam('status') == 'reject'){
					$model->setStatus(VES_VendorsCredit_Model_Withdrawal::STATUS_CANCELED)->save();
					
					/*Send notification email*/
	    			Mage::helper('vendorscredit')->sendRejectedWithdrawalNotificationEmail($model);
	    			
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('vendorscredit')->__('Withdrawal has been rejected.'));
				}else{
					/*Complete the withdrawal*/
					$data = array(
			    		'vendor'	=> $model->getVendor(),
			    		'type'		=> 'withdrawal',
			    		'amount'	=> $model->getAmount(),
			    		'fee'		=> 0,
						'withdrawal'=> $model,
			    	);
			    	Mage::getModel('vendorscredit/type')->process($data);
	    	
					$model->setStatus(VES_VendorsCredit_Model_Withdrawal::STATUS_COMPLETE)->save();
					/*Send notification email*/
	    			Mage::helper('vendorscredit')->sendSuccessWithdrawalNotificationEmail($model);
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('vendorscredit')->__('Withdrawal has been marked as complete'));
				}
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/view', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('vendorscredit')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
}