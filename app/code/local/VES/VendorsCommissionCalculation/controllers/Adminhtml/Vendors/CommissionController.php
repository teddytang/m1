<?php

class VES_VendorsCommissionCalculation_Adminhtml_Vendors_CommissionController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/vendors/commission');
    }
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('vendors/vendors')
            ->_addBreadcrumb(Mage::helper('vendorscommission')->__('Vendors'), Mage::helper('vendorscommission')->__('Vendors'))
            ->_addBreadcrumb(Mage::helper('vendorscommission')->__('Commission Configuration'), Mage::helper('vendorscommission')->__('Commission Configuration'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}
	
	public function newAction()
	{
	    $this->_forward('edit');
	}
	
    public function editAction()
    {
        $this->_title($this->__('Vendors'))->_title($this->__('Commission Configuration'));

        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('vendorscommission/rule');

        if ($id) {
            $model->load($id);
            if (! $model->getRuleId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('catalogrule')->__('This rule no longer exists.')
                );
                $this->_redirect('*/*');
                return;
            }
        }

        $this->_title($model->getRuleId() ? $model->getName() : $this->__('New Rule'));

        // set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        $model->getConditions()->setJsFormObject('rule_conditions_fieldset');

        Mage::register('current_commission_rule', $model);
        $this->loadLayout();
        $this->renderLayout();

    }
	
    public function saveAction()
    {
        if ($this->getRequest()->getPost()) {
            try {
                $model = Mage::getModel('vendorscommission/rule');
                
                $data = $this->getRequest()->getPost();
                $data = $this->_filterDates($data, array('from_date', 'to_date'));
                if ($id = $this->getRequest()->getParam('rule_id')) {
                    $model->load($id);
                    if ($id != $model->getId()) {
                        Mage::throwException(Mage::helper('catalogrule')->__('Wrong rule specified.'));
                    }
                }
    
                $validateResult = $model->validateData(new Varien_Object($data));
                if ($validateResult !== true) {
                    foreach($validateResult as $errorMessage) {
                        $this->_getSession()->addError($errorMessage);
                    }
                    $this->_getSession()->setPageData($data);
                    $this->_redirect('*/*/edit', array('id'=>$model->getId()));
                    return;
                }
    
                $data['conditions'] = $data['rule']['conditions'];
                unset($data['rule']);
    
                $autoApply = false;
                if (!empty($data['auto_apply'])) {
                    $autoApply = true;
                    unset($data['auto_apply']);
                }
                if(isset($data['website_ids']) && is_array($data['website_ids'])) $data['website_ids'] = implode(',', $data['website_ids']);
                if(isset($data['vendor_group_ids']) && is_array($data['vendor_group_ids'])) $data['vendor_group_ids'] = implode(',', $data['vendor_group_ids']);

                $model->loadPost($data);
    
                Mage::getSingleton('adminhtml/session')->setPageData($model->getData());
    
                $model->save();
    
                Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('catalogrule')->__('The rule has been saved.')
                );
                Mage::getSingleton('adminhtml/session')->setPageData(false);
                if ($autoApply) {
                    $this->getRequest()->setParam('rule_id', $model->getId());
                    $this->_forward('applyRules');
                } else {
                    Mage::getModel('catalogrule/flag')->loadSelf()
                    ->setState(1)
                    ->save();
                    if ($this->getRequest()->getParam('back')) {
                        $this->_redirect('*/*/edit', array('id' => $model->getId()));
                        return;
                    }
                    $this->_redirect('*/*/');
                }
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(
                    Mage::helper('catalogrule')->__('An error occurred while saving the rule data. Please review the log and try again.')
                );
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->setPageData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('rule_id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }
    
    public function deleteAction() {
        if( $id = $this->getRequest()->getParam('id') ) {
            try {
                $model = Mage::getModel('vendorscommission/rule')->setId($id);
                $model->delete();
    
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }
	
		 public function massDeleteAction() {
        $ruleIds = $this->getRequest()->getParam('vendors');
        if(!is_array($ruleIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($ruleIds as $ruleId) {
                    $rule = Mage::getModel('vendorscommission/rule')->load($ruleId);
                    $rule->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($ruleIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction()
    {
        $ruleIds = $this->getRequest()->getParam('vendors');
        if(!is_array($ruleIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($ruleIds as $ruleId) {
                    $vendorsrma = Mage::getSingleton('vendorscommission/rule')
                        ->load($ruleId)
                        ->setIsActive($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($ruleIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	

}