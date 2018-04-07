<?php
class VES_VendorsCredit_Credit_WithdrawController extends VES_Vendors_Controller_Action
{
	public function indexAction(){
		$this->loadLayout()
		->_setActiveMenu('credit')->_title($this->__('Credit'))->_title($this->__('Withdraw'))
    	->_addBreadcrumb(Mage::helper('vendorscredit')->__('Credit'), Mage::helper('vendorscredit')->__('Credit'))
    	->_addBreadcrumb(Mage::helper('vendorscredit')->__('Withdrawal'), Mage::helper('vendorscredit')->__('Withdrawal'));
		$this->renderLayout();
	}
	
	public function viewAction(){
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('vendorscredit/withdrawal')->load($id);
		if ($model->getId()) {
			Mage::register('withdrawal_data', $model);
			$this->loadLayout()
			->_setActiveMenu('credit')->_title($this->__('Credit'))->_title($this->__('Withdraw'))
	    	->_addBreadcrumb(Mage::helper('vendorscredit')->__('Credit'), Mage::helper('vendorscredit')->__('Credit'))
	    	->_addBreadcrumb(Mage::helper('vendorscredit')->__('Withdrawal'), Mage::helper('vendorscredit')->__('Withdrawal'))
	    	->_addBreadcrumb(Mage::helper('vendorscredit')->__('View Withdrawal'), Mage::helper('vendorscredit')->__('View Withdrawal'));;
	    	$this->renderLayout();
		}else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('vendorscredit')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
	
	public function formAction(){
		$this->loadLayout();
		$this->_setActiveMenu('credit')
		->_setActiveMenu('credit')->_title($this->__('Credit'))->_title($this->__('Withdraw'))
    	->_addBreadcrumb(Mage::helper('vendorscredit')->__('Credit'), Mage::helper('vendorscredit')->__('Credit'))
    	->_addBreadcrumb(Mage::helper('vendorscredit')->__('Withdraw'), Mage::helper('vendorscredit')->__('Withdraw'))
    	->_addBreadcrumb(Mage::helper('vendorscredit')->__('Form'), Mage::helper('vendorscredit')->__('Form'));
    	
    	$methodId = $this->getRequest()->getParam('method');
    	if($methodId){
	    	$method = Mage::getModel('vendorscredit/payment')->load($methodId);
	    	Mage::register('payment_method', $method);
			$this->renderLayout();
    	}else{
    		$this->_getSession()->addError(Mage::helper('vendorscredit')->__('Please select a payment method.'));
    		$this->_redirect('*/*/');
    	}
	}
	
	public function formPostAction(){
		try{
			$data = $this->getRequest()->getParams();
			/*Validate Method*/
			$method = $data['method'];
			if(!$method) throw new Exception(Mage::helper('vendorscredit')->__('Please select a payment method.'));
			$method = Mage::getModel('vendorscredit/payment')->load($method);
			if(!$method->getId()) throw new Exception(Mage::helper('vendorscredit')->__('Please select a payment method.'));
			
			/*Validate amount*/
			$amount = $data['amount'];
			if(!is_numeric($amount)) throw new VES_Vendors_Exception(Mage::helper('vendorscredit')->__('Please enter a valid amount.'));
			if(($method->getMax() > 0) && $amount > $method->getMax()) throw new VES_Vendors_Exception(Mage::helper('vendorscredit')->__('The withdrawal amount must be less than %s.',Mage::helper('core')->currency($method->getMax(),true,false)));
			if(($method->getMin() > 0) && $amount < $method->getMin()) throw new VES_Vendors_Exception(Mage::helper('vendorscredit')->__('The withdrawal amount must be greater than %s.',Mage::helper('core')->currency($method->getMin(),true,false)));
			
			$vendor = Mage::getSingleton('vendors/session')->getVendor();
			if($amount > $vendor->getCredit()) throw new VES_Vendors_Exception(Mage::helper('vendorscredit')->__('The withdrawal amount must be less than your balance.'));
			$withdrawalCollection = Mage::getModel('vendorscredit/withdrawal')->getCollection()
				->addFieldToFilter('vendor_id',$vendor->getId())
				->addFieldToFilter('status',VES_VendorsCredit_Model_Withdrawal::STATUS_PENDING);
			if($withdrawalCollection->count()){
				$pendingAmount = 0;
				foreach($withdrawalCollection as $withdrawal){$pendingAmount+= $withdrawal->getAmount();}
				
				if(($amount+$pendingAmount) > $vendor->getCredit()) throw new VES_Vendors_Exception(Mage::helper('vendorscredit')->__('The withdrawal amount must be less than your balance.'));
			}
			/*Validate account*/
			$methodAdditionalInfo = unserialize($method->getAdditionalInfo());
			if(isset($methodAdditionalInfo['allow_email_account']) && $methodAdditionalInfo['allow_email_account'] && !$data['account']) throw new VES_Vendors_Exception(Mage::helper('vendorscredit')->__('Please enter your %s email account.',$method->getName()));
			if(isset($methodAdditionalInfo['allow_additional_textarea']) && $methodAdditionalInfo['allow_additional_textarea'] && !$data['additional_info']) throw new VES_Vendors_Exception(Mage::helper('vendorscredit')->__('Please enter your %s account info.',$method->getName()));
			/*Redirect to review page*/
			$this->_redirect('*/*/review',array('data'=>base64_encode(serialize($data))));
		}catch (VES_Vendors_Exception $e){
			$this->_getSession()->addError($e->getMessage());
    		$this->_redirect('*/*/form',array('method'=>$method->getId()));
		}catch(Exception $e){
			$this->_getSession()->addError($e->getMessage());
    		$this->_redirect('*/*/');
		}
	}
	
	public function reviewAction(){
		$data = $this->getRequest()->getParam('data');
		$data = unserialize(base64_decode($data));
		
    	$methodId = $data['method'];    
    	if(!$methodId){
    		$this->_getSession()->addError(Mage::helper('vendorscredit')->__('Please select a payment method.'));
    		$this->_redirect('*/*/');
    		return;
    	}
    	
    	$amount = $data['amount'];
		if(!$amount){
    		$this->_getSession()->addError(Mage::helper('vendorscredit')->__('Please enter your withdrawal information.'));
    		$this->_redirect('*/*/');
    		return;
    	}
    	
    	$method = Mage::getModel('vendorscredit/payment')->load($methodId);
    	Mage::register('payment_method', $method);
    	Mage::register('withdrawal_data', $data);
    	$this->loadLayout()
		->_setActiveMenu('credit')->_title($this->__('Credit'))->_title($this->__('Withdraw'))->_title($this->__('Review'))
    	->_addBreadcrumb(Mage::helper('vendorscredit')->__('Credit'), Mage::helper('vendorscredit')->__('Credit'))
    	->_addBreadcrumb(Mage::helper('vendorscredit')->__('Withdraw'), Mage::helper('vendorscredit')->__('Withdraw'))
    	->_addBreadcrumb(Mage::helper('vendorscredit')->__('Review'), Mage::helper('vendorscredit')->__('Review'));
		$this->renderLayout();
	}
	
	public function reviewPostAction(){
		try{
			$data = $this->getRequest()->getParam('data');
			$data = unserialize(base64_decode($data));
			$methodId = $data['method'];    
	    	if(!$methodId){
	    		$this->_getSession()->addError(Mage::helper('vendorscredit')->__('Please select a payment method.'));
	    		$this->_redirect('*/*/');
	    		return;
	    	}
	    	
	    	$amount = $data['amount'];
			if(!$amount){
	    		$this->_getSession()->addError(Mage::helper('vendorscredit')->__('Please enter your withdrawal information.'));
	    		$this->_redirect('*/*/');
	    		return;
	    	}
	    	$vendor = Mage::getSingleton('vendors/session')->getVendor();
	    	$method = Mage::getModel('vendorscredit/payment')->load($methodId);
	    	$withdrawal = Mage::getModel('vendorscredit/withdrawal');
	    	$withdrawal->setData(array(
	    		'vendor_id'	=> $vendor->getId(),
	    		'method'	=> $method->getName(),
	    		'amount'	=> $amount,
	    		'fee'		=> $method->getFee(),
	    		'net_amount'=> $amount-$method->getFee(),
	    		'additional_info'	=> $this->getRequest()->getParam('data'),
	    		'status'			=> VES_VendorsCredit_Model_Withdrawal::STATUS_PENDING,
	    		'created_at'		=> now(),
	    		'updated_at'		=> now(),
	    	))
	    	->save();
	    	/*Send notification email*/
	    	Mage::helper('vendorscredit')->sendNewWithdrawalNotificationEmail($withdrawal);
	    	
	    	$this->_getSession()->addSuccess(Mage::helper('vendorscredit')->__('Your withdrawal has been saved and awaiting for approval.'));
		}catch (Exception $e){
			$this->_getSession()->addError($e->getMessage());
		}
    	$this->_redirect('*/*/');
	}
	
	
	public function historyAction(){
		$this->loadLayout()
		->_setActiveMenu('credit')->_title($this->__('Credit'))->_title($this->__('Withdrawal History'))
    	->_addBreadcrumb(Mage::helper('vendorscredit')->__('Credit'), Mage::helper('vendorscredit')->__('Credit'))
    	->_addBreadcrumb(Mage::helper('vendorscredit')->__('Withdrawal History'), Mage::helper('vendorscredit')->__('Withdrawal History'));
		$this->renderLayout();
	}
	
	public function deleteAction(){
		try{
			$id     = $this->getRequest()->getParam('id');
			if ($id) {
				$model  = Mage::getModel('vendorscredit/withdrawal')->setId($id)->delete();
				$this->_getSession()->addSuccess(Mage::helper('vendorscredit')->__('Your withdrawal has been canceled.'));
				
			} else {
				$this->_getSession()->addError(Mage::helper('vendorscredit')->__('Item does not exist.'));
			}
		}catch (Exception $e){
			$this->_getSession()->addError($e->getMessage());
		}
		$this->_redirect('*/*/history');
	}
	
	public function resubmitAction(){
		try{
			$id     = $this->getRequest()->getParam('id');
			if ($id) {
				$model  = Mage::getModel('vendorscredit/withdrawal')->load($id);
				$model->setStatus(VES_VendorsCredit_Model_Withdrawal::STATUS_PENDING)->save();
				$this->_getSession()->addSuccess(Mage::helper('vendorscredit')->__('Your withdrawal has been resubmited for approval.'));
				
			} else {
				$this->_getSession()->addError(Mage::helper('vendorscredit')->__('Item does not exist.'));
			}
		}catch (Exception $e){
			$this->_getSession()->addError($e->getMessage());
		}
		$this->_redirect('*/*/history');
	}
}