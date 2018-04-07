<?php

/**
 * Vendor product attribute set
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsProduct_Adminhtml_Vendors_Catalog_Product_SetController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/vendors/catalog/attributeset');
    }
    
    protected function _init(){
        $this->_title($this->__('Vendors'))
        ->_title($this->__('Manage Product Attribute Sets'));
        $this->_setTypeId();
        $this->loadLayout();
        $this->_setActiveMenu('vendors/catalog');
    }
    /**
     * Define in register catalog_product entity type code as entityType
     *
     */
    protected function _setTypeId()
    {
        Mage::register('entityType',
        Mage::getModel('catalog/product')->getResource()->getTypeId());
    }
    
    /**
     * Retrieve catalog product entity type id
     *
     * @return int
     */
    protected function _getEntityTypeId()
    {
        if (is_null(Mage::registry('entityType'))) {
            $this->_setTypeId();
        }
        return Mage::registry('entityType');
    }
    public function indexAction(){
        $this->_init();        
        $this->_addBreadcrumb(Mage::helper('vendorsproduct')->__('Vendors'), Mage::helper('vendorsproduct')->__('Vendors'));
        $this->_addBreadcrumb(
            Mage::helper('vendorsproduct')->__('Manage Product Attribute Sets'),
            Mage::helper('vendorsproduct')->__('Manage Product Attribute Sets'));
        
        $this->renderLayout();
    }
    public function setGridAction()
    {
        $this->_setTypeId();
        $this->getResponse()->setBody(
            $this->getLayout()
            ->createBlock('vendorsproduct/adminhtml_catalog_product_attribute_set_grid')
            ->toHtml());
    }
    public function editAction()
    {
        $attributeSet = Mage::getModel('eav/entity_attribute_set')
        ->load($this->getRequest()->getParam('id'));
    
        if (!$attributeSet->getId()) {
            $this->_redirect('*/*/index');
            return;
        }
        
        Mage::register('current_attribute_set', $attributeSet);
        $vendorSet = Mage::getModel('vendorsproduct/catalog_product_attribute_set')->load($attributeSet->getId(),'parent_set_id');
        Mage::register('vendor_attribute_set', $vendorSet);
        
        $this->_init();
        $this->_title($attributeSet->getId() ? $attributeSet->getAttributeSetName() : $this->__('New Set'));
    

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);    
        $this->renderLayout();
    }
    
    
    /**
     * Save attribute set action
     *
     * [POST] Create attribute set from another set and redirect to edit page
     * [AJAX] Save attribute set data
     *
     */
    public function saveAction()
    {
        $entityTypeId   = $this->_getEntityTypeId();
        $hasError       = false;
        $attributeSetId = $this->getRequest()->getParam('id', false);
        $parentSetId    = $this->getRequest()->getParam('parent_id', false);
        $isNewSet       = $this->getRequest()->getParam('gotoEdit', false) == '1';
    
      
            /* @var $model Mage_Eav_Model_Entity_Attribute_Set */
            $parentSet  = Mage::getModel('eav/entity_attribute_set')
            ->setEntityTypeId($entityTypeId);
            
            if ($parentSetId) {
                $parentSet->load($parentSetId);
            }
            if (!$parentSet->getId()) {
                Mage::throwException(Mage::helper('catalog')->__('This attribute set no longer exists.'));
            }
            
            $model  = Mage::getModel('vendorsproduct/catalog_product_attribute_set')->load($attributeSetId);
            
            if(!$model->getId()){
                $model->setAttributeSetName($parentSet->getAttributeSetName());
                $model->setParentSetId($parentSet->getId());
                $model->save();
                $model->setIsNewObject(true);
            }

            /** @var $helper Mage_Adminhtml_Helper_Data */
            $helper = Mage::helper('adminhtml');
            
            $data = Mage::helper('core')->jsonDecode($this->getRequest()->getPost('data'));
            
            $model->setGroupDisplayType($data['group_display_type']);
            
            //filter html tags
            $data['attribute_set_name'] = $helper->stripTags($data['attribute_set_name']);

            $model->organizeData($data);
    
            $model->validate();

            $model->save();
            $this->_getSession()->addSuccess(Mage::helper('catalog')->__('The attribute set has been saved.'));

    

        $response = array();
        if ($hasError) {
            $this->_initLayoutMessages('adminhtml/session');
            $response['error']   = 1;
            $response['message'] = $this->getLayout()->getMessagesBlock()->getGroupedHtml();
        } else {
            $response['error']   = 0;
            $response['url']     = $this->getUrl('*/*/');
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));

    }
    
} 
