<?php
class VES_VendorsImportProduct_Export_IndexController extends VES_Vendors_Controller_Action
{   
	protected function _isAllowed()
    {
        return Mage::helper('vendorsimport')->moduleEnable();
    }
    
	protected function _initProfile()
    {
        $this->_title($this->__('System'))
             ->_title($this->__('Import and Export'))
             ->_title($this->__('Profiles'));

        $profileId = (int) $this->getRequest()->getParam('id');
        $profile = Mage::getModel('vendorsimport/profile');

        if ($profileId) {
            $profile->load($profileId);
            if (!$profile->getId()) {
                $this->_getSession()->addError(
                    $this->__('The profile import profile is not set. Please contact administrator about this problem.'));
                $this->_redirect('*/*');
                return false;
            }
        }
		$profile->setVendor($this->_getSession()->getVendor());
        Mage::register('current_convert_profile', $profile);

        return $this;
    }
    
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('vendorsimport')
			->_title($this->__('Import/Export'))
			->_title($this->__('Export'))
			->_addBreadcrumb($this->__('Import/Export'), $this->__('Import/Export'))
			->_addBreadcrumb($this->__('Export'), $this->__('Export'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}
	public function exportAction() {
		$this->_initAction()
			->renderLayout();
	}
	
	public function uploadAction()
    {
        try {
            $vendor = $this->_getSession()->getVendor();
            $helper	= Mage::helper('vendorsimport');
        	$uploader = new Mage_Core_Model_File_Uploader('Filedata');
            $uploader->setAllowedExtensions(array('csv'));
            $uploader->setAllowRenameFiles(false);
            $uploader->setFilesDispersion(false);
            $result = $uploader->save(
                $helper->getVendorImportFolder($vendor)
            );

            /**
             * Workaround for prototype 1.7 methods "isJSON", "evalJSON" on Windows OS
             */
            $result['tmp_name'] = str_replace(DS, "/", $result['tmp_name']);
            $result['path'] 	= str_replace(DS, "/", $result['path']);
            
            $result['file_name']= $result['file'];
            $result['file_size']= Mage::getModel('directory/currency')->format($result['size'],array('display'=>Zend_Currency::NO_SYMBOL,precision=>0),false);
            $result['last_modified']	= Mage::app()->getLocale()->date(filemtime($result['path'] .$result['file'] ))->toString(Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM));
            $result['url'] 				= $helper->getVendorImportFileUrl($result['file'],$vendor);
            $result['thumbnail_url']	= Mage::getDesign()->getSkinUrl('ves_vendors/importproduct/icons/file.png');
            $result['success']	= true;

        } catch (Exception $e) {
            $result = array(
            	'success'	=> false,
                'error' => $e->getMessage(),
                'errorcode' => $e->getCode());
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
    
    public function massDeleteAction(){
    	$vendor = $this->_getSession()->getVendor();
    	$helper = Mage::helper('vendorsimport');
    	$files 	= explode(',',$this->getRequest()->getParam('filename'));
    	foreach($files as $file){
    		@unlink(Mage::helper('vendorsimport')->getVendorExportFile($file, $vendor));
    	}
    	
    	$this->_redirect('*/*');
    }
    
	public function runAction()
    {
        $this->_initProfile();
        $this->loadLayout();
        $this->renderLayout();
    }
    
	public function batchRunAction()
    {
        if ($this->getRequest()->isPost()) {
            $batchId = $this->getRequest()->getPost('batch_id', 0);
            $rowIds  = $this->getRequest()->getPost('rows');

            /* @var $batchModel Mage_Dataflow_Model_Batch */
            $batchModel = Mage::getModel('dataflow/batch')->load($batchId);

            if (!$batchModel->getId()) {
                return;
            }
            if (!is_array($rowIds) || count($rowIds) < 1) {
                return;
            }
            if (!$batchModel->getAdapter()) {
                return;
            }

            $batchImportModel = $batchModel->getBatchImportModel();
            $importIds = $batchImportModel->getIdCollection();

            $adapter = Mage::getModel($batchModel->getAdapter());
            $adapter->setBatchParams($batchModel->getParams());

            $errors = array();
            $saved  = 0;
            foreach ($rowIds as $importId) {
                $batchImportModel->load($importId);
                if (!$batchImportModel->getId()) {
                    $errors[] = Mage::helper('dataflow')->__('Skip undefined row.');
                    continue;
                }

                try {
                    $importData = $batchImportModel->getBatchData();
                    $adapter->saveRow($importData);
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                    continue;
                }
                $saved ++;
            }

            if (method_exists($adapter, 'getEventPrefix')) {
                /**
                 * Event for process rules relations after products import
                 */
                Mage::dispatchEvent($adapter->getEventPrefix() . '_finish_before', array(
                    'adapter' => $adapter
                ));

                /**
                 * Clear affected ids for adapter possible reuse
                 */
                $adapter->clearAffectedEntityIds();
            }

            $result = array(
                'savedRows' => $saved,
                'errors'    => $errors
            );
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    public function batchFinishAction()
    {
        $batchId = $this->getRequest()->getParam('id');
        if ($batchId) {
            $batchModel = Mage::getModel('dataflow/batch')->load($batchId);
            /* @var $batchModel Mage_Dataflow_Model_Batch */

            if ($batchModel->getId()) {
                $result = array();
                try {
                    $batchModel->beforeFinish();
                } catch (Mage_Core_Exception $e) {
                    $result['error'] = $e->getMessage();
                } catch (Exception $e) {
                    $result['error'] = Mage::helper('adminhtml')->__('An error occurred while finishing process. Please refresh the cache');
                }
                $batchModel->delete();
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            }
        }
    }
}