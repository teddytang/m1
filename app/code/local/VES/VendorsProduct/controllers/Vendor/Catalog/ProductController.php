<?php
class VES_VendorsProduct_Vendor_Catalog_ProductController extends VES_Vendors_Controller_Action
{
	/**
     * The greatest value which could be stored in CatalogInventory Qty field
     */
    const MAX_QTY_VALUE = 99999999.9999;
	/**
     * Initialize product from request parameters
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _initProduct()
    {
        $this->_title($this->__('Catalog'))->_title($this->__('Manage Products'));

        $productId  = (int) $this->getRequest()->getParam('id');
      //  $product    = Mage::getModel('vendorsproduct/catalog_product')
	  $product    = Mage::getModel('catalog/product')
               ->setStoreId($this->getRequest()->getParam('store', 0));
		$product->isObjectNew(true);
        if (!$productId) {
            if ($setId = (int) $this->getRequest()->getParam('set')) {
                $product->setAttributeSetId($setId);
            }

            if ($typeId = $this->getRequest()->getParam('type')) {
                $product->setTypeId($typeId);
            }
        }

        $product->setData('_edit_mode', true);
        if ($productId) {
            try {
                $product->load($productId);
            } catch (Exception $e) {
                $product->setTypeId(Mage_Catalog_Model_Product_Type::DEFAULT_TYPE);
                Mage::logException($e);
            }
        }

        $attributes = $this->getRequest()->getParam('attributes');
        if ($attributes && $product->isConfigurable() &&
            (!$productId || !$product->getTypeInstance()->getUsedProductAttributeIds())) {
            $product->getTypeInstance()->setUsedProductAttributeIds(
                explode(",", base64_decode(urldecode($attributes)))
            );
        }

        // Required attributes of simple product for configurable creation
        if ($this->getRequest()->getParam('popup')
            && $requiredAttributes = $this->getRequest()->getParam('required')) {
            $requiredAttributes = explode(",", $requiredAttributes);
            foreach ($product->getAttributes() as $attribute) {
                if (in_array($attribute->getId(), $requiredAttributes)) {
                    $attribute->setIsRequired(1);
                }
            }
        }

        if ($this->getRequest()->getParam('popup')
            && $this->getRequest()->getParam('product')
            && !is_array($this->getRequest()->getParam('product'))
            && $this->getRequest()->getParam('id', false) === false) {

            $configProduct = Mage::getModel('catalog/product')
                ->setStoreId(0)
                ->load($this->getRequest()->getParam('product'))
                ->setTypeId($this->getRequest()->getParam('type'));

            /* @var $configProduct Mage_Catalog_Model_Product */
            $data = array();
            foreach ($configProduct->getTypeInstance()->getEditableAttributes() as $attribute) {

                /* @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
                if(!$attribute->getIsUnique()
                    && $attribute->getFrontend()->getInputType()!='gallery'
                    && $attribute->getAttributeCode() != 'required_options'
                    && $attribute->getAttributeCode() != 'has_options'
                    && $attribute->getAttributeCode() != $configProduct->getIdFieldName()) {
                    $data[$attribute->getAttributeCode()] = $configProduct->getData($attribute->getAttributeCode());
                }
            }

            $product->addData($data)
                ->setWebsiteIds($configProduct->getWebsiteIds());
        }

        Mage::register('product', $product);
        Mage::register('current_product', $product);
        Mage::getSingleton('vendorsimage/wysiwyg_config')->setStoreId($this->getRequest()->getParam('store'));
		//var_dump($product->getOrigData("tier_price"));exit;
        return $product;
    }
	 /**
     * Create serializer block for a grid
     *
     * @param string $inputName
     * @param Mage_Adminhtml_Block_Widget_Grid $gridBlock
     * @param array $productsArray
     * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Ajax_Serializer
     */
    protected function _createSerializerBlock($inputName, Mage_Adminhtml_Block_Widget_Grid $gridBlock, $productsArray)
    {
        return $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_ajax_serializer')
            ->setGridBlock($gridBlock)
            ->setProducts($productsArray)
            ->setInputElementName($inputName)
        ;
    }

    /**
     * Output specified blocks as a text list
     */
    protected function _outputBlocks()
    {
        $blocks = func_get_args();
        $output = $this->getLayout()->createBlock('adminhtml/text_list');
        foreach ($blocks as $block) {
            $output->insert($block, '', true);
        }
        $this->getResponse()->setBody($output->toHtml());
    }
    
    /**
     * Product Manage page
     */
	public function indexAction()
    {
    	$this->loadLayout();
    	$this->_setActiveMenu('catalog')->_title($this->__('Catalog'))->_title($this->__('Manage Products'));
    	$this->_addBreadcrumb(Mage::helper('vendorsproduct')->__('Catalog'), Mage::helper('vendorsproduct')->__('Catalog'));
    	$this->_addBreadcrumb(Mage::helper('vendorsproduct')->__('Manage Products'), Mage::helper('vendorsproduct')->__('Manage Products'));
		$this->renderLayout();
    }
	/**
     * Create new product page
     */
    public function newAction()
    {
        $product = $this->_initProduct();

        $this->_title($this->__('New Product'));

        Mage::dispatchEvent('catalog_product_new_action', array('product' => $product));

        if ($this->getRequest()->getParam('popup')) {
            $this->loadLayout('popup');
        } else {
            $_additionalLayoutPart = '';
            if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE
                && !($product->getTypeInstance()->getUsedProductAttributeIds()))
            {
                $_additionalLayoutPart = '_new';
            }
            $this->loadLayout(array(
                'default',
                strtolower($this->getFullActionName()),
                'adminhtml_catalog_product_'.$product->getTypeId() . $_additionalLayoutPart
            ));
            $this->_addBreadcrumb(Mage::helper('vendorsproduct')->__('Catalog'), Mage::helper('vendorsproduct')->__('Catalog'))
            	->_addBreadcrumb(Mage::helper('vendorsproduct')->__('Manage Products'), Mage::helper('vendorsproduct')->__('Manage Products'),Mage::getUrl('vendors/catalog_product'))
            	->_addBreadcrumb(Mage::helper('vendorsproduct')->__('New Product'), Mage::helper('vendorsproduct')->__('New Product'));
            $this->_setActiveMenu('catalog/products');
        }

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $block = $this->getLayout()->getBlock('catalog.wysiwyg.js');
        if ($block) {
            $block->setStoreId($product->getStoreId());
        }

        $this->renderLayout();
    }
	/**
     * Product edit form
     */
    public function editAction()
    {
        $productId  = (int) $this->getRequest()->getParam('id');
        $product = $this->_initProduct();
		if($product->getVendorId() != $this->_getSession()->getVendor()->getId()){
			$this->_getSession()->addError(Mage::helper('catalog')->__('You do not have permission to access this page.'));
            $this->_redirect('*/*/');
            return;
		}
        if ($productId && !$product->getId()) {
            $this->_getSession()->addError(Mage::helper('catalog')->__('This product no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }

        $this->_title($product->getName());

        Mage::dispatchEvent('catalog_product_edit_action', array('product' => $product));

        $_additionalLayoutPart = '';
        if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE
            && !($product->getTypeInstance()->getUsedProductAttributeIds()))
        {
            $_additionalLayoutPart = '_new';
        }

        $this->loadLayout(array(
            'default',
            strtolower($this->getFullActionName()),
            'adminhtml_catalog_product_'.$product->getTypeId() . $_additionalLayoutPart
        ));
		$this->_addBreadcrumb(Mage::helper('vendorsproduct')->__('Catalog'), Mage::helper('vendorsproduct')->__('Catalog'))
            	->_addBreadcrumb(Mage::helper('vendorsproduct')->__('Manage Products'), Mage::helper('vendorsproduct')->__('Manage Products'),Mage::getUrl('vendors/catalog_product'))
            	->_addBreadcrumb(Mage::helper('vendorsproduct')->__('Edit Product'), Mage::helper('vendorsproduct')->__('Edit Product'));
        $this->_setActiveMenu('catalog/products');

        if (!Mage::app()->isSingleStoreMode() && ($switchBlock = $this->getLayout()->getBlock('store_switcher'))) {
            $switchBlock->setDefaultStoreName($this->__('Default Values'))
                ->setWebsiteIds($product->getWebsiteIds())
                ->setSwitchUrl(
                    Mage::getUrl('*/*/*', array('_current'=>true, 'active_tab'=>null, 'tab' => null, 'store'=>null))
                );
        }

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $block = $this->getLayout()->getBlock('catalog.wysiwyg.js');
        if ($block) {
            $block->setStoreId($product->getStoreId());
        }

        $this->renderLayout();
    }
	/**
     * WYSIWYG editor action for ajax request
     *
     */
    public function wysiwygAction()
    {
        $elementId = $this->getRequest()->getParam('element_id', md5(microtime()));
        $storeId = $this->getRequest()->getParam('store_id', 0);
        $storeMediaUrl = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
		$this->loadLayout();
        $content = $this->getLayout()->createBlock('vendorsimage/cms_helper_form_wysiwyg_content', '', array(
            'editor_element_id' => $elementId,
            'store_id'          => $storeId,
            'store_media_url'   => $storeMediaUrl,
        ));
        $this->getResponse()->setBody($content->toHtml());
    }

    /**
     * Product grid for AJAX request
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    /**
     * Validate product
     *
     */
    public function validateAction()
    {
        $response = new Varien_Object();
        $response->setError(false);

        try {
            $productData = $this->getRequest()->getPost('product');

            if ($productData && !isset($productData['stock_data']['use_config_manage_stock'])) {
                $productData['stock_data']['use_config_manage_stock'] = 0;
            }
            /* @var $product Mage_Catalog_Model_Product */
            $product = Mage::getModel('catalog/product');
            $product->setData('_edit_mode', true);
            if ($storeId = $this->getRequest()->getParam('store')) {
                $product->setStoreId($storeId);
            }
            if ($setId = $this->getRequest()->getParam('set')) {
                $product->setAttributeSetId($setId);
            }
            if ($typeId = $this->getRequest()->getParam('type')) {
                $product->setTypeId($typeId);
            }
            if ($productId = $this->getRequest()->getParam('id')) {
                $product->load($productId);
            }

            $dateFields = array();
            $attributes = $product->getAttributes();
            foreach ($attributes as $attrKey => $attribute) {
                if ($attribute->getBackend()->getType() == 'datetime') {
                    if (array_key_exists($attrKey, $productData) && $productData[$attrKey] != ''){
                        $dateFields[] = $attrKey;
                    }
                }
            }
            $productData = $this->_filterDates($productData, $dateFields);

            $product->addData($productData);
            if(!$product->getVendorId()){
	            $vendorId = $this->_getSession()->getVendor()->getId();
				$product->setData('vendor_id',$vendorId);
            }
			
			/* set restrictions for date ranges */
            $resource = $product->getResource();
            $resource->getAttribute('special_from_date')
                ->setMaxValue($product->getSpecialToDate());
            $resource->getAttribute('news_from_date')
                ->setMaxValue($product->getNewsToDate());
            $resource->getAttribute('custom_design_from')
                ->setMaxValue($product->getCustomDesignTo());
				
            $product->validate();
            /**
             * @todo implement full validation process with errors returning which are ignoring now
             */
//            if (is_array($errors = $product->validate())) {
//                foreach ($errors as $code => $error) {
//                    if ($error === true) {
//                        Mage::throwException(Mage::helper('catalog')->__('Attribute "%s" is invalid.', $product->getResource()->getAttribute($code)->getFrontend()->getLabel()));
//                    }
//                    else {
//                        Mage::throwException($error);
//                    }
//                }
//            }
        }
        catch (Mage_Eav_Model_Entity_Attribute_Exception $e) {
            $response->setError(true);
            $response->setAttribute($e->getAttributeCode());
            $response->setMessage($e->getMessage());
        } catch (Mage_Core_Exception $e) {
            $response->setError(true);
            $response->setMessage($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_initLayoutMessages('adminhtml/session');
            $response->setError(true);
            $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
        }

        $this->getResponse()->setBody($response->toJson());
    }
	/**
     * Initialize product before saving
     */
    protected function _initProductSave($currentStoreId)
    {
        $product     = $this->_initProduct();

    	if(!$product->getVendorId()){
        	$vendorId = $this->_getSession()->getVendor()->getId();
			$product->setData('vendor_id',$vendorId);
        }
		if(!$product->getId()){
			if(Mage::helper('vendorsproduct')->isProductApproval()){
				$product->setData('approval',VES_VendorsProduct_Model_Source_Approval::STATUS_NOT_SUBMITED);
			}else{
				$product->setData('approval',VES_VendorsProduct_Model_Source_Approval::STATUS_APPROVED);
			}
		}
		
        $productData = $this->getRequest()->getPost('product');
        if ($productData) {
            $this->_filterStockData($productData['stock_data']);
        }
		
		
		Mage::app()->setCurrentStore($currentStoreId);

        /**
         * Websites
         */
        if (!isset($productData['website_ids'])) {
            $productData['website_ids'] = array(Mage::app()->getWebsite()->getId());
        }

        $wasLockedMedia = false;
        if ($product->isLockedAttribute('media')) {
            $product->unlockAttribute('media');
            $wasLockedMedia = true;
        }

        $product->addData($productData);

        if ($wasLockedMedia) {
            $product->lockAttribute('media');
        }

        if (Mage::app()->isSingleStoreMode()) {
		//echo "tesst";exit;
            $product->setWebsiteIds(array(Mage::app()->getStore(true)->getWebsite()->getId()));
        }
		
		Mage::app()->getStore()->setId($currentStoreId);
      
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

        /**
         * Create Permanent Redirect for old URL key
         */
        if ($product->getId() && isset($productData['url_key_create_redirect']))
        // && $product->getOrigData('url_key') != $product->getData('url_key')
        {
            $product->setData('save_rewrites_history', (bool)$productData['url_key_create_redirect']);
        }

        /**
         * Check "Use Default Value" checkboxes values
         */
        if ($useDefaults = $this->getRequest()->getPost('use_default')) {
            foreach ($useDefaults as $attributeCode) {
                $product->setData($attributeCode, false);
            }
        }

        /**
         * Init product links data (related, upsell, crosssel)
         */
        $links = $this->getRequest()->getPost('links');
        if (isset($links['related']) && !$product->getRelatedReadonly()) {
            $product->setRelatedLinkData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['related']));
        }
        if (isset($links['upsell']) && !$product->getUpsellReadonly()) {
            $product->setUpSellLinkData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['upsell']));
        }
        if (isset($links['crosssell']) && !$product->getCrosssellReadonly()) {
            $product->setCrossSellLinkData(Mage::helper('adminhtml/js')
                ->decodeGridSerializedInput($links['crosssell']));
        }
        if (isset($links['grouped']) && !$product->getGroupedReadonly()) {
            $product->setGroupedLinkData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['grouped']));
        }

        /**
         * Initialize product categories
         */
        $categoryIds = $this->getRequest()->getPost('category_ids');
        if (null !== $categoryIds) {
            if (empty($categoryIds)) {
                $categoryIds = array();
            }
            $product->setCategoryIds($categoryIds);
        }

        /**
         * Initialize data for configurable product
         */
        if (($data = $this->getRequest()->getPost('configurable_products_data'))
            && !$product->getConfigurableReadonly()
        ) {
            $product->setConfigurableProductsData(Mage::helper('core')->jsonDecode($data));
        }
        if (($data = $this->getRequest()->getPost('configurable_attributes_data'))
            && !$product->getConfigurableReadonly()
        ) {
            $product->setConfigurableAttributesData(Mage::helper('core')->jsonDecode($data));
        }

        $product->setCanSaveConfigurableAttributes(
            (bool) $this->getRequest()->getPost('affect_configurable_product_attributes')
                && !$product->getConfigurableReadonly()
        );

        /**
         * Initialize product options
         */
        if (isset($productData['options']) && !$product->getOptionsReadonly()) {
            $product->setProductOptions($productData['options']);
        }

        $product->setCanSaveCustomOptions(
            (bool)$this->getRequest()->getPost('affect_product_custom_options')
            && !$product->getOptionsReadonly()
        );

		
        Mage::dispatchEvent(
            'catalog_product_prepare_save',
            array('product' => $product, 'request' => $this->getRequest())
        );
    	
		//echo "test";exit;
		
        return $product;
    }

    /**
     * Filter product stock data
     *
     * @param array $stockData
     */
    protected function _filterStockData(&$stockData) {
        if (!isset($stockData['use_config_manage_stock'])) {
            $stockData['use_config_manage_stock'] = 0;
        }
        if (isset($stockData['qty']) && (float)$stockData['qty'] > self::MAX_QTY_VALUE) {
            $stockData['qty'] = self::MAX_QTY_VALUE;
        }
        if (isset($stockData['min_qty']) && (int)$stockData['min_qty'] < 0) {
            $stockData['min_qty'] = 0;
        }
        if (!isset($stockData['is_decimal_divided']) || $stockData['is_qty_decimal'] == 0) {
            $stockData['is_decimal_divided'] = 0;
        }
    }
    
 	/**
     * Save product action
     */
    public function saveAction()
    {
		
        $storeId        = $this->getRequest()->getParam('store');
        $redirectBack   = $this->getRequest()->getParam('back', false);
        $productId      = $this->getRequest()->getParam('id');
        $isEdit         = (int)($this->getRequest()->getParam('id') != null);

        $data = $this->getRequest()->getPost();
        if ($data) {
            $this->_filterStockData($data['product']['stock_data']);
			$currentStoreId = Mage::app()->getStore()->getId();
			//Mage::app()->getStore()->setId(0);
			Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
            $product = $this->_initProductSave($currentStoreId);
          //  Mage::app()->getStore()->setId($currentStoreId);
            //adding
            Mage::dispatchEvent('ves_vendorsproduct_before_save',array('model'=>$product));
            try {
				//Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
                $product->save();
				Mage::app()->setCurrentStore($currentStoreId);
				
                $productId = $product->getId();

                /**
                 * Do copying data to stores
                 */
                if (isset($data['copy_to_stores'])) {
                    foreach ($data['copy_to_stores'] as $storeTo=>$storeFrom) {
                        $newProduct = Mage::getModel('catalog/product')
                            ->setStoreId($storeFrom)
                            ->load($productId)
                            ->setStoreId($storeTo)
                            ->save();
                    }
                }

                Mage::getModel('catalogrule/rule')->applyAllRulesToProduct($product);

                $this->_getSession()->addSuccess(Mage::helper("vendorsproduct")->__('The product has been saved.'));
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage())
                    ->setProductData($data);
                $redirectBack = true;
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($e->getMessage());
                $redirectBack = true;
            }
        }

        if ($redirectBack) {
            $this->_redirect('*/*/edit', array(
                'id'    => $productId,
                '_current'=>true
            ));
        } elseif($this->getRequest()->getParam('popup')) {
            $this->_redirect('*/*/created', array(
                '_current'   => true,
                'id'         => $productId,
                'edit'       => $isEdit
            ));
        } else {
            $this->_redirect('*/*/', array('store'=>$storeId));
        }
    }
    
	/**
     * Get categories fieldset block
     *
     */
    public function categoriesAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->renderLayout();
    }
	
	/**
     * Get categories fieldset block
     *
     */
    public function vendorcategoriesAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function categoriesJsonAction()
    {
        $product = $this->_initProduct();

        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('vendorsproduct/vendor_product_edit_tab_categories')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }
    
    
	/**
     * Get options fieldset block
     *
     */
    public function optionsAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->renderLayout();
    }
    
	/**
     * Delete product action
     */
    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $product = Mage::getModel('catalog/product')
                ->load($id);
            $sku = $product->getSku();
            try {
                $product->delete();
                $this->_getSession()->addSuccess(Mage::helper("vendorsproduct")->__('The product has been deleted.'));
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->getResponse()
            ->setRedirect(Mage::getUrl('*/*/'));
    }
    
	public function massDeleteAction()
    {
        $productIds = explode(",",$this->getRequest()->getParam('product'));
		
		 
        if (!is_array($productIds)) {
            $this->_getSession()->addError(Mage::helper("vendorsproduct")->__('Please select product(s).'));
        } else {
            if (!empty($productIds)) {
                try {
                    foreach ($productIds as $productId) {
                        $product = Mage::getSingleton('catalog/product')->load($productId);
                        Mage::dispatchEvent('catalog_controller_product_delete', array('product' => $product));
                        $product->delete();
                    }
                    $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) have been deleted.', count($productIds))
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Update product(s) status action
     *
     */
    public function massStatusAction()
    {
        $productIds = (array)explode(',',$this->getRequest()->getParam('product'));
        $storeId    = (int)$this->getRequest()->getParam('store', 0);
        $status     = (int)$this->getRequest()->getParam('status');

        try {
            $this->_validateMassStatus($productIds, $status);
            Mage::getSingleton('catalog/product_action')
                ->updateAttributes($productIds, array('status' => $status), $storeId);

            $this->_getSession()->addSuccess(
                Mage::helper("vendorsproduct")->__('Total of %d record(s) have been updated.', count($productIds))
            );
        }
        catch (Mage_Core_Model_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()
                ->addException($e, Mage::helper("vendorsproduct")->__('An error occurred while updating the product(s) status.'));
        }

        $this->_redirect('*/*/', array('store'=> $storeId));
    }

    /**
     * Validate batch of products before theirs status will be set
     *
     * @throws Mage_Core_Exception
     * @param  array $productIds
     * @param  int $status
     * @return void
     */
    public function _validateMassStatus(array $productIds, $status)
    {
        if ($status == Mage_Catalog_Model_Product_Status::STATUS_ENABLED) {
            if (!Mage::getModel('catalog/product')->isProductsHasSku($productIds)) {
                throw new Mage_Core_Exception(
                    Mage::helper("vendorsproduct")->__('Some of the processed products have no SKU value defined. Please fill it prior to performing operations on these products.')
                );
            }
        }
    }
	/**
     * Create duplicate
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _duplicateProduct($product)
    {
        $product->getWebsiteIds();
        $product->getCategoryIds();

        /* @var $newProduct Mage_Catalog_Model_Product */
        $newProduct = Mage::getModel('catalog/product')->setData($product->getData())
            ->setIsDuplicate(true)
            ->setOriginalId($product->getId())
            ->setSku(null)
            ->setVendorSku(null)
            ->setStatus(Mage_Catalog_Model_Product_Status::STATUS_DISABLED)
            ->setCreatedAt(null)
            ->setUpdatedAt(null)
            ->setId(null)
            ->setStoreId(0);

        Mage::dispatchEvent(
            'catalog_model_product_duplicate',
            array('current_product' => $product, 'new_product' => $newProduct)
        );

        /* Prepare Related*/
        $data = array();
        $product->getLinkInstance()->useRelatedLinks();
        $attributes = array();
        foreach ($product->getLinkInstance()->getAttributes() as $_attribute) {
            if (isset($_attribute['code'])) {
                $attributes[] = $_attribute['code'];
            }
        }
        foreach ($product->getRelatedLinkCollection() as $_link) {
            $data[$_link->getLinkedProductId()] = $_link->toArray($attributes);
        }
        $newProduct->setRelatedLinkData($data);

        /* Prepare UpSell*/
        $data = array();
        $product->getLinkInstance()->useUpSellLinks();
        $attributes = array();
        foreach ($product->getLinkInstance()->getAttributes() as $_attribute) {
            if (isset($_attribute['code'])) {
                $attributes[] = $_attribute['code'];
            }
        }
        foreach ($product->getUpSellLinkCollection() as $_link) {
            $data[$_link->getLinkedProductId()] = $_link->toArray($attributes);
        }
        $newProduct->setUpSellLinkData($data);

        /* Prepare Cross Sell */
        $data = array();
        $product->getLinkInstance()->useCrossSellLinks();
        $attributes = array();
        foreach ($product->getLinkInstance()->getAttributes() as $_attribute) {
            if (isset($_attribute['code'])) {
                $attributes[] = $_attribute['code'];
            }
        }
        foreach ($product->getCrossSellLinkCollection() as $_link) {
            $data[$_link->getLinkedProductId()] = $_link->toArray($attributes);
        }
        $newProduct->setCrossSellLinkData($data);

        /* Prepare Grouped */
        $data = array();
        $product->getLinkInstance()->useGroupedLinks();
        $attributes = array();
        foreach ($product->getLinkInstance()->getAttributes() as $_attribute) {
            if (isset($_attribute['code'])) {
                $attributes[] = $_attribute['code'];
            }
        }
        foreach ($product->getGroupedLinkCollection() as $_link) {
            $data[$_link->getLinkedProductId()] = $_link->toArray($attributes);
        }
        $newProduct->setGroupedLinkData($data);

        $newProduct->save();

        $product->getOptionInstance()->duplicate($product->getId(), $newProduct->getId());
        $product->getResource()->duplicate($product->getId(), $newProduct->getId());

        // TODO - duplicate product on all stores of the websites it is associated with
        /*if ($storeIds = $this->getWebsiteIds()) {
            foreach ($storeIds as $storeId) {
                $this->setStoreId($storeId)
                   ->load($this->getId());

                $newProduct->setData($this->getData())
                    ->setSku(null)
                    ->setStatus(Mage_Catalog_Model_Product_Status::STATUS_DISABLED)
                    ->setId($newId)
                    ->save();
            }
        }*/
        return $newProduct;
    }
	/**
     * Create product duplicate
     */
    public function duplicateAction()
    {
        $product = $this->_initProduct();
        try {
            $newProduct = $this->_duplicateProduct($product);
            $this->_getSession()->addSuccess($this->__('The product has been duplicated.'));
            $this->_redirect('*/*/edit', array('_current'=>true, 'id'=>$newProduct->getId()));
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*/edit', array('_current'=>true));
        }
    }
	public function quickCreateAction()
    {
        $result = array();

        /* @var $configurableProduct Mage_Catalog_Model_Product */
        $configurableProduct = Mage::getModel('catalog/product')
            ->setStoreId(Mage_Core_Model_App::ADMIN_STORE_ID)
            ->load($this->getRequest()->getParam('product'));

        if (!$configurableProduct->isConfigurable()) {
            // If invalid parent product
            $this->_redirect('*/*/');
            return;
        }

        /* @var $product Mage_Catalog_Model_Product */

        $product = Mage::getModel('catalog/product')
            ->setStoreId(0)
            ->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_SIMPLE)
            ->setAttributeSetId($configurableProduct->getAttributeSetId());


        foreach ($product->getTypeInstance()->getEditableAttributes() as $attribute) {
            if ($attribute->getIsUnique()
                || $attribute->getAttributeCode() == 'url_key'
                || $attribute->getFrontend()->getInputType() == 'gallery'
                || $attribute->getFrontend()->getInputType() == 'media_image'
                || !$attribute->getIsVisible()) {
                continue;
            }

            $product->setData(
                $attribute->getAttributeCode(),
                $configurableProduct->getData($attribute->getAttributeCode())
            );
        }

        $product->addData($this->getRequest()->getParam('simple_product', array()));
        $product->setWebsiteIds($configurableProduct->getWebsiteIds());

        $autogenerateOptions = array();
        $result['attributes'] = array();

        foreach ($configurableProduct->getTypeInstance()->getConfigurableAttributes() as $attribute) {
            $value = $product->getAttributeText($attribute->getProductAttribute()->getAttributeCode());
            $autogenerateOptions[] = $value;
            $result['attributes'][] = array(
                'label'         => $value,
                'value_index'   => $product->getData($attribute->getProductAttribute()->getAttributeCode()),
                'attribute_id'  => $attribute->getProductAttribute()->getId()
            );
        }

        if ($product->getNameAutogenerate()) {
            $product->setName($configurableProduct->getName() . '-' . implode('-', $autogenerateOptions));
        }
        
		$vendor = $this->_getSession()->getVendor();
		$product->setVendorId($vendor->getId());
        if ($product->getSkuAutogenerate()) {
        	$sku = $configurableProduct->getVendorSku() . '-' . implode('-', $autogenerateOptions);
            $product->setVendorSku($sku);
            $product->setSku($vendor->getVendorId().'_'.$sku);
        }

        if (is_array($product->getPricing())) {
           $result['pricing'] = $product->getPricing();
           $additionalPrice = 0;
           foreach ($product->getPricing() as $pricing) {
               if (empty($pricing['value'])) {
                   continue;
               }

               if (!empty($pricing['is_percent'])) {
                   $pricing['value'] = ($pricing['value']/100)*$product->getPrice();
               }

               $additionalPrice += $pricing['value'];
           }

           $product->setPrice($product->getPrice() + $additionalPrice);
           $product->unsPricing();
        }

        try {
            /**
             * @todo implement full validation process with errors returning which are ignoring now
             */
//            if (is_array($errors = $product->validate())) {
//                $strErrors = array();
//                foreach($errors as $code=>$error) {
//                    $codeLabel = $product->getResource()->getAttribute($code)->getFrontend()->getLabel();
//                    $strErrors[] = ($error === true)? Mage::helper('catalog')->__('Value for "%s" is invalid.', $codeLabel) : Mage::helper('catalog')->__('Value for "%s" is invalid: %s', $codeLabel, $error);
//                }
//                Mage::throwException('data_invalid', implode("\n", $strErrors));
//            }

            $product->validate();
            $product->save();
            $result['product_id'] = $product->getId();
            $this->_getSession()->addSuccess(Mage::helper('catalog')->__('The product has been created.'));
            $this->_initLayoutMessages('adminhtml/session');
            $result['messages']  = $this->getLayout()->getMessagesBlock()->getGroupedHtml();
        } catch (Mage_Core_Exception $e) {
            $result['error'] = array(
                'message' =>  $e->getMessage(),
                'fields'  => array(
                    'sku'  =>  $product->getSku()
                )
            );

        } catch (Exception $e) {
            Mage::logException($e);
            $result['error'] = array(
                'message'   =>  $this->__('An error occurred while saving the product. ') . $e->getMessage()
             );
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
	/**
     * Get associated grouped products grid and serializer block
     */
    public function superGroupAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('vendor.catalog.product.edit.tab.super.group')
            ->setProductsGrouped($this->getRequest()->getPost('products_grouped', null));
        $this->renderLayout();
    }

    /**
     * Get associated grouped products grid only
     *
     */
    public function superGroupGridOnlyAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('vendor.catalog.product.edit.tab.super.group')
            ->setProductsGrouped($this->getRequest()->getPost('products_grouped', null));
        $this->renderLayout();
    }
	/**
     * Get super config grid
     *
     */
    public function superConfigAction()
    {
        $this->_initProduct();
        $this->loadLayout(false);
        $this->renderLayout();
    }
    
	public function createdAction()
    {
        $this->_getSession()->addNotice(
            Mage::helper('catalog')->__('Please click on the Close Window button if it is not closed automatically.')
        );
        $this->loadLayout('popup');
        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/catalog_product_created')
        );
        $this->renderLayout();
    }
    
    public function submitforapprovalAction(){
    	$productId = $this->getRequest()->getParam('id');
		//echo 	$productId;exit;
    	if ($productId) {
			try {
				$currentStoreId = Mage::app()->getStore()->getId();
				Mage::app()->getStore()->setId(0);
				$product = Mage::getModel('catalog/product')->load($productId);
				$product->setData('approval',VES_VendorsProduct_Model_Source_Approval::STATUS_PENDING)->getResource()->saveAttribute($product,'approval');
				$product->save();
				Mage::app()->getStore()->setId($currentStoreId);
				
				$this->_getSession()->addSuccess(
					$this->__('Product "%s" have been submited for approval.', $product->getName())
				);
			} catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
			}
		}
        $this->_redirect('*/*/edit',array('id'=>$productId));
    }
	
    public function massApprovalAction(){
    	$productIds = explode(",",$this->getRequest()->getParam('product'));
    	try{
	    	foreach($productIds as $productId){
	    		$currentStoreId = Mage::app()->getStore()->getId();
				Mage::app()->getStore()->setId(0);
				$product = Mage::getModel('catalog/product')->load($productId);
	
				$product->setData('approval',VES_VendorsProduct_Model_Source_Approval::STATUS_PENDING);

				$product->save();
				Mage::app()->getStore()->setId($currentStoreId);
	    	}
	    	$this->_getSession()->addSuccess(
				$this->__('%s items have been submited for approval.', sizeof($productIds))
			);
    	}catch(Mage_Core_Exception $e){
    		$this->_getSession()->addError($e->getMessage());
    	}
    	
    	$this->_redirect('*/*/');
    }
	
	/**
     * Get related products grid and serializer block
     */
    public function relatedAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('catalog.product.edit.tab.related')
            ->setProductsRelated($this->getRequest()->getPost('products_related', null));
        $this->renderLayout();
    }
    
	/**
     * Get related products grid
     */
    public function relatedGridAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('catalog.product.edit.tab.related')
            ->setProductsRelated($this->getRequest()->getPost('products_related', null));
        $this->renderLayout();
    }
}