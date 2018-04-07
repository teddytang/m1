<?php

class VES_Vendors_Adminhtml_VendorsController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/vendors/vendors');
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
	public function addAction(){
		$this->_forward('edit');
	}
	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('vendors/vendor')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('vendors_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('vendors/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('vendors/adminhtml_vendors_edit'))
				->_addLeft($this->getLayout()->createBlock('vendors/adminhtml_vendors_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('vendors')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			$model = Mage::getModel('vendors/vendor');
			
			if(isset($_FILES['logo']['name']) && $_FILES['logo']['name'] != '') {
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('logo');
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(true);
					$uploader->setFilesDispersion(true);
					$path = Mage::getBaseDir('media') . DS."ves_vendors".DS."logo".DS ;
					$uploader->save($path, $_FILES['logo']['name']);
					$data['logo'] = "ves_vendors/logo".$uploader->getUploadedFileName();
				} catch (Exception $e) {
		      		
		        }
			}else{
				if(isset($data['logo']['delete']) && $data['logo']['delete']){
					$data['logo'] = '';
				}else{
					$data['logo'] = $data['logo']['value'];
				}
			}
			$model->load($this->getRequest()->getParam('id'));
			if($data['new_password']){
				$data['password'] = $data['confirmation'] = $data['new_password'];
			}
			foreach($data as $key=>$value){
				$model->setData($key,$value);
			}
			try {
				if ($model->getCreatedTime() == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}
				if(!$model->getId()) $model->setIsNewVendor(true);
				
				$model->save();
				if($model->getIsNewVendor()){
				    /*Send new vendor account email */
    				$vendorStoreId = Mage::app()->getWebsite($model->getWebsiteId())->getDefaultGroup()->getDefaultStoreId();
    				if ($model->isConfirmationRequired()) {
    					$model->sendNewAccountEmail(
    						'confirmation',
    						'',
    						$vendorStoreId
    					);
    				} else {
    					if(Mage::helper('vendors')->approvalRequired()){
    						$model->sendNewAccountEmail('registered','',$vendorStoreId);
    					}else{
    						$model->sendNewAccountEmail(
    				            'registered',
    				            '',
    				            $vendorStoreId
    				        );
    					}
    				}
				}
				
				Mage::dispatchEvent('adminhtml_vendor_save_after',array('vendor'=>$model));
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('vendors')->__('Item was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('vendors')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('vendors/vendor')->load($this->getRequest()->getParam('id'));
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
        $vendorsIds = $this->getRequest()->getParam('vendors');
        if(!is_array($vendorsIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($vendorsIds as $vendorsId) {
                    $vendors = Mage::getModel('vendors/vendor')->load($vendorsId);
                    $vendors->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($vendorsIds)
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
        $vendorsIds = $this->getRequest()->getParam('vendors');
        if(!is_array($vendorsIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($vendorsIds as $vendorsId) {
                    $vendor = Mage::getSingleton('vendors/vendor')
                        ->load($vendorsId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true);
                        
					if($vendor->getStatus() == VES_Vendors_Model_Vendor::STATUS_ACTIVATED && !$vendor->getData("is_sendmail_active_vendor")){
						if(Mage::getStoreConfig('vendors/create_account/send_approved')){
							$vendor->sendNewAccountEmail("active");
							$vendor->setData("is_sendmail_active_vendor",1);
						}
					}
					else{
						$vendor->setData("is_sendmail_active_vendor",0);
					}
					$vendor->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($vendorsIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'vendors.csv';
        $content    = $this->getLayout()->createBlock('vendors/adminhtml_vendors_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'vendors.xml';
        $content    = $this->getLayout()->createBlock('vendors/adminhtml_vendors_grid')
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