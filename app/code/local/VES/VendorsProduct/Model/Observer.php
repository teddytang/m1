<?php

class VES_VendorsProduct_Model_Observer
{
    /**
     * Set duplicated product
     * @param Varien_Event_Observer $observer
     */
    public function catalog_model_product_duplicate(Varien_Event_Observer $observer){
    	if(!Mage::helper('vendors')->moduleEnabled()){
    		return;
    	}
    	
    	$newProduct = $observer->getNewProduct();
    	if(Mage::helper('vendorsproduct')->isProductApproval()){
			$newProduct->setData('approval',VES_VendorsProduct_Model_Source_Approval::STATUS_NOT_SUBMITED);
		}else{
			$newProduct->setData('approval',VES_VendorsProduct_Model_Source_Approval::STATUS_APPROVED);
		}        
    }
    
    /**
     * Validate Vendor SKU
     * @param Varien_Event_Observer $observer
     */
    public function catalog_product_validate_before(Varien_Event_Observer $observer){
        /*Do nothing if the extension is disabled*/
        if(!Mage::helper('vendors')->moduleEnabled()) return;
        
    	$product	= $observer->getProduct();
    	if($vendorId = $product->getVendorId()){
    		$request = Mage::app()->getFrontController()->getRequest();
    		$key = $request->getControllerName().'_'.$request->getActionName();
    		if(Mage::app()->getStore()->isAdmin() && $key == 'catalog_product_quickCreate'){
    			/*Do not validate if the action is quick create simple associated product*/
    			
    			$vendor = Mage::getModel('vendors/vendor')->load($vendorId);
    			$sku 	= $product->getSku();
    			$sku 	= str_replace($vendor->getVendorId().'_', '', $sku);
    			$product->setVendorSku($sku);
    			$product->setData('sku',$vendor->getVendorId().'_'.$product->getVendorSku());
    			return;
    		}
    		
	    	$resource = Mage::getSingleton('core/resource');
			$tableName = $resource->getTableName('catalog_product_entity');
			$readConnection = $resource->getConnection('core_read');
			$query = 'SELECT * FROM ' . $resource->getTableName('catalog/product').' WHERE vendor_sku="'.$product->getVendorSku().'" AND vendor_id="'.$vendorId.'" AND entity_id <> "'.$product->getId().'";';
	    	$results = $readConnection->fetchAll($query);
	    	if(sizeof($results)){
	    		$exception = new Mage_Eav_Model_Entity_Attribute_Exception(Mage::helper('vendorsproduct')->__('The SKU is already exist.')); 
	    		$exception->setAttributeCode('vendor_sku');
	    		throw $exception;
	    	}
    	}
    }
    
    /**
     * Set value for SKU by join vendor ID and Vendor SKU.
     * @param Varien_Event_Observer $observer
     */
	public function catalog_product_prepare_save(Varien_Event_Observer $observer) {
	    /*Do nothing if the extension is disabled*/
	    if(!Mage::helper('vendors')->moduleEnabled()) return;

        $request = $observer->getEvent()->getRequest();
        $product = $observer->getEvent()->getProduct();

		if($vendorId = $product->getVendorId()) {
			$vendor = Mage::getModel('vendors/vendor')->load($vendorId);
    		$product->setData('sku',$vendor->getVendorId().'_'.$product->getVendorSku());
    	}

        if($product->getVendorSkuType()){
            $product->setSkuType($product->getVendorSkuType());
        }

        if (($items = $request->getPost('bundle_options')) && !$product->getCompositeReadonly()) {
            $product->setBundleOptionsData($items);
        }

        if (($selections = $request->getPost('bundle_selections')) && !$product->getCompositeReadonly()) {
            $product->setBundleSelectionsData($selections);
        }

        if ($product->getPriceType() == '0' && !$product->getOptionsReadonly()) {
            $product->setCanSaveCustomOptions(true);
            if ($customOptions = $product->getProductOptions()) {
                foreach (array_keys($customOptions) as $key) {
                    $customOptions[$key]['is_delete'] = 1;
                }
                $product->setProductOptions($customOptions);
            }
        }

        $product->setCanSaveBundleSelections(
            (bool)$request->getPost('affect_bundle_product_selections') && !$product->getCompositeReadonly()
        );

    }
    
	/**
     * set template
     * @param Varien_Event_Observer $observer
     */
    public function catalog_product_gallery_prepare(Varien_Event_Observer $observer){
        /*Do nothing if the extension is disabled*/
        if(!Mage::helper('vendors')->moduleEnabled()) return;
        
        $block = $observer->getBlock();
        $block->setTemplate('ves_vendorsproduct/product/helper/gallery.phtml');
    }


    /**
     * Add price index data for catalog product collection
     * only for front end
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Bundle_Model_Observer
     */
    public function loadProductOptions($observer)
    {
        // echo "test";exit;
        $collection = $observer->getEvent()->getCollection();
        /* @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection */

        if(!Mage::getSingleton('vendors/session')->getVendorId())
            $collection->addPriceData();

        return $this;
    }

    /**
     * Setting attribute tab block for bundle
     *
     * @param Varien_Object $observer
     * @return Mage_Bundle_Model_Observer
     */
    public function setAttributeTabBlock($observer)
    {
        $product = $observer->getEvent()->getProduct();
        if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
            Mage::helper('adminhtml/catalog')
                ->setAttributeTabBlock('vendorsproduct/vendor_product_edit_tab_bundle_attributes');
        }
        return $this;
    }

    /**
     * duplicating bundle options and selections
     *
     * @param Varien_Object $observer
     * @return Mage_Bundle_Model_Observer
     */
    public function duplicateProduct($observer)
    {
        $product = $observer->getEvent()->getCurrentProduct();

        if ($product->getTypeId() != Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
            //do nothing if not bundle
            return $this;
        }

        $newProduct = $observer->getEvent()->getNewProduct();

        $product->getTypeInstance(true)->setStoreFilter($product->getStoreId(), $product);
        $optionCollection = $product->getTypeInstance(true)->getOptionsCollection($product);
        $selectionCollection = $product->getTypeInstance(true)->getSelectionsCollection(
            $product->getTypeInstance(true)->getOptionsIds($product),
            $product
        );
        $optionCollection->appendSelections($selectionCollection);

        $optionRawData = array();
        $selectionRawData = array();

        $i = 0;
        foreach ($optionCollection as $option) {
            $optionRawData[$i] = array(
                'required' => $option->getData('required'),
                'position' => $option->getData('position'),
                'type' => $option->getData('type'),
                'title' => $option->getData('title')?$option->getData('title'):$option->getData('default_title'),
                'delete' => ''
            );
            foreach ($option->getSelections() as $selection) {
                $selectionRawData[$i][] = array(
                    'product_id' => $selection->getProductId(),
                    'position' => $selection->getPosition(),
                    'is_default' => $selection->getIsDefault(),
                    'selection_price_type' => $selection->getSelectionPriceType(),
                    'selection_price_value' => $selection->getSelectionPriceValue(),
                    'selection_qty' => $selection->getSelectionQty(),
                    'selection_can_change_qty' => $selection->getSelectionCanChangeQty(),
                    'delete' => ''
                );
            }
            $i++;
        }

        $newProduct->setBundleOptionsData($optionRawData);
        $newProduct->setBundleSelectionsData($selectionRawData);
        return $this;
    }


    /**
     * Append selection attributes to selection's order item
     *
     * @param Varien_Object $observer
     * @return Mage_Bundle_Model_Observer
     */
    public function appendBundleSelectionData($observer)
    {
        $orderItem = $observer->getEvent()->getOrderItem();
        $quoteItem = $observer->getEvent()->getItem();

        if ($attributes = $quoteItem->getProduct()->getCustomOption('bundle_selection_attributes')) {
            $productOptions = $orderItem->getProductOptions();
            $productOptions['bundle_selection_attributes'] = $attributes->getValue();
            $orderItem->setProductOptions($productOptions);
        }

        return $this;
    }

}