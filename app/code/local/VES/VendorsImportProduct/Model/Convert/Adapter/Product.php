<?php

class VES_VendorsImportProduct_Model_Convert_Adapter_Product extends Mage_Catalog_Model_Convert_Adapter_Product
{
		
	 /**
     * Categories text-path to ID hash.
     *
     * @var array
     */
	protected $_vendorcategories = array();
	 
    protected $_categories = array();

    /**
     * Categories text-path to ID hash with roots checking.
     *
     * @var array
     */
    protected $_categoriesWithRoots = array();
	protected $_vendorcategoriesWithRoots = array();
	
	
    public function getVendor(){
        return Mage::getSingleton('vendors/session')->getVendor();;
    }
    /**
     * Load product collection Id(s)
     */
    public function load()
    {
        $attrFilterArray = array();
        $attrFilterArray ['name']           = 'like';
        $attrFilterArray ['sku']            = 'startsWith';
        $attrFilterArray ['type']           = 'eq';
        $attrFilterArray ['attribute_set']  = 'eq';
        $attrFilterArray ['visibility']     = 'eq';
        $attrFilterArray ['status']         = 'eq';
        $attrFilterArray ['price']          = 'fromTo';
        $attrFilterArray ['qty']            = 'fromTo';
        $attrFilterArray ['store_id']       = 'eq';

        $attrToDb = array(
            'type'          => 'type_id',
            'attribute_set' => 'attribute_set_id'
        );

        $filters = $this->_parseVars();

        if ($qty = $this->getFieldValue($filters, 'qty')) {
            $qtyFrom = isset($qty['from']) ? (float) $qty['from'] : 0;
            $qtyTo   = isset($qty['to']) ? (float) $qty['to'] : 0;

            $qtyAttr = array();
            $qtyAttr['alias']       = 'qty';
            $qtyAttr['attribute']   = 'cataloginventory/stock_item';
            $qtyAttr['field']       = 'qty';
            $qtyAttr['bind']        = 'product_id=entity_id';
            $qtyAttr['cond']        = "{{table}}.qty between '{$qtyFrom}' AND '{$qtyTo}'";
            $qtyAttr['joinType']    = 'inner';

            $this->setJoinField($qtyAttr);
        }

        Mage_Eav_Model_Convert_Adapter_Entity::setFilter($attrFilterArray, $attrToDb);

        if ($price = $this->getFieldValue($filters, 'price')) {
            $this->_filter[] = array(
                'attribute' => 'price',
                'from'      => $price['from'],
                'to'        => $price['to']
            );
            $this->setJoinAttr(array(
                'alias'     => 'price',
                'attribute' => 'catalog_product/price',
                'bind'      => 'entity_id',
                'joinType'  => 'LEFT'
            ));
        }

        /*Add filter for current vendor*/
        $this->_filter[] = array(
            'attribute' => 'vendor_id',
            'eq'      => $this->getVendor()->getId(),
        );

        return Mage_Eav_Model_Convert_Adapter_Entity::load();
    }

    /**
     * Save product (import)
     *
     * @param  array $importData
     * @throws Mage_Core_Exception
     * @return bool
     */
    public function saveRow(array $importData)
    {
		
		
        $vendor = $this->getVendor();
		
		$this->_initCategories()->_initCategoriesVendor($vendor);

        $importData['vendor_id'] 	= $vendor->getId();
        $importData['vendor_sku'] 	= $importData['sku'];
        $importData['sku']			= $vendor->getVendorId()."_".$importData['sku'];

        $product = $this->getProductModel()
            ->reset();

        if (empty($importData['store'])) {
            if (!is_null($this->getBatchParams('store'))) {
                $store = $this->getStoreById($this->getBatchParams('store'));
            } else {
                $message = Mage::helper('catalog')->__('Skipping import row, required field "%s" is not defined.', 'store');
                Mage::throwException($message);
            }
        } else {
            $store = $this->getStoreByCode($importData['store']);
        }

        if ($store === false) {
            $message = Mage::helper('catalog')->__('Skipping import row, store "%s" field does not exist.', $importData['store']);
            Mage::throwException($message);
        }

        if (empty($importData['sku'])) {
            $message = Mage::helper('catalog')->__('Skipping import row, required field "%s" is not defined.', 'sku');
            Mage::throwException($message);
        }
        $product->setStoreId($store->getId());
        $productId = $product->getIdBySku($importData['sku']);

        if ($productId) {
            $product->load($productId);
        } else {
            $productTypes = $this->getProductTypes();
            $productAttributeSets = $this->getProductAttributeSets(); //Mage::log('set');Mage::log($productAttributeSets);

            /**
             * Check product define type
             */
            if (empty($importData['type']) || !isset($productTypes[strtolower($importData['type'])])) {
                $value = isset($importData['type']) ? $importData['type'] : '';
                $message = Mage::helper('catalog')->__('Skip import row, is not valid value "%s" for field "%s"', $value, 'type');
                Mage::throwException($message);
            }
            $product->setTypeId($productTypes[strtolower($importData['type'])]);
            /**
             * Check product define attribute set
             */
            if (empty($importData['attribute_set']) || !isset($productAttributeSets[$importData['attribute_set']])) {
                $value = isset($importData['attribute_set']) ? $importData['attribute_set'] : '';
                $message = Mage::helper('catalog')->__('Skip import row, the value "%s" is invalid for field "%s"', $value, 'attribute_set');
                Mage::throwException($message);
            }
            $product->setAttributeSetId($productAttributeSets[$importData['attribute_set']]);

            foreach ($this->_requiredFields as $field) {
                $attribute = $this->getAttribute($field);
                if (!isset($importData[$field]) && $attribute && $attribute->getIsRequired()) {
                    $message = Mage::helper('catalog')->__('Skipping import row, required field "%s" for new products is not defined.', $field);
                    Mage::throwException($message);
                }
            }
        }

        $mediaGalleryBackendModel = Mage::getModel('vendorsimport/catalog_product_attribute_backend_media')->setAttribute($this->getAttribute('media_gallery'));//$this->getAttribute('media_gallery')->getBackend();
        $mediaGalleryBackendModel->setVendor($vendor);
        $arrayToMassAdd = array();
        $addedImages = array();
        foreach ($product->getMediaAttributes() as $mediaAttributeCode => $mediaAttribute) {
            if (isset($importData[$mediaAttributeCode])) {
                $filesImport = trim($importData[$mediaAttributeCode]);
				$files = explode(";",$filesImport);
				foreach($files as $file){
					
					//var_dump($file);exit;
					if(empty($file)) continue;
					$originalFile = $file;
					/*If vendor use an url for the image*/
					if(strpos($file, 'http://') !== false || strpos($file, 'https://')){
						$file = $this->saveImageToVendorFolder($file,$vendor);
					}
					
					$file = $vendor->getVendorId().'/media/'.$file;
					if($mediaGalleryBackendModel->getImage($product, $file)) continue;
					
					$importData[$mediaAttributeCode] = $file;
					if (!$mediaGalleryBackendModel->getImage($product, $file)) {
						$arrayToMassAdd[] = array('file' => trim($file), 'mediaAttribute' => $mediaAttributeCode);
						$addedImages[] = $originalFile;
					}
				}
            }
        }

        
        //var_dump($product->getData('media_gallery'));exit;
        $this->setProductTypeInstance($product);
        /*Process Categories*/
        /*
        if (isset($importData['category_ids'])) {
            $product->setCategoryIds($importData['category_ids']);
        }*/

        if (isset($importData['categories'])) {
            //$categoryIds = array();
           // $categoryNameArr = explode(",", $importData['categories']);
           // $categories = Mage::getModel('catalog/category')->getCollection()->addAttributeToFilter('name',array('in'=>$categoryNameArr));
            //$categoryIds = $categories->getAllIds();
			$categoryIds =  $this->getCategoryIds($importData['categories']);
            $product->setCategoryIds($categoryIds);
        }

		
		/**
		* for vendor_categories
		*/
		if (isset($importData['vendor_categories'])) {
          //  $categoryIds = array();
          //  $categoryNameArr = explode(",", $importData['vendor_categories']);
          //  $categories = Mage::getModel('vendorscategory/category')->getCollection()->addFieldToFilter('name',array('in'=>$categoryNameArr))->addFieldToFilter('vendor_id', $vendor->getId());
         //   $categoryIds = $categories->getAllIds();
		 
		 	$categoryIds =  $this->getCategoryIdVendor($importData['vendor_categories']);
            $product->setData("vendor_categories",$categoryIds);
        }
		
		
        /**
         * set product configurable
         */
        if(isset($importData['type'])) {
            if($importData['type'] == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
                $configurableProductData = array();
                $configurableAttributesData = array();
                $simples_skus = $importData['simples_skus'];$simples_skus = explode(',',$simples_skus); //Mage::log($simples_skus);
                $super_attribute_pricing = $importData['super_attribute_pricing'];
                $configurable_attributes = $importData['configurable_attributes'];
                $configurable_attributes = explode(',',$configurable_attributes);
                $attributeDatas = array();
                //Mage::log($configurable_attributes);

                /**
                 * get all attributes and options data
                 */
                foreach($configurable_attributes as $attribute_code) {
                    $attribute_details = Mage::getSingleton("eav/config")->getAttribute("catalog_product", $attribute_code);
                    //Mage::log($attribute_details->getData());
                    $options = $attribute_details->getSource()->getAllOptions(false);
                    $data = array(
                        'attribute_id'  =>$attribute_details->getId(),
                        'code'          =>$attribute_code,
                        'label'         => $attribute_details->getData('frontend_label'),
                        'frontend_label'    => $attribute_details->getData('frontend_label'),
                    );

                    $configurableAttrData = array(
                        'label'     =>      $attribute_details->getData('frontend_label'),
                        'frontend_label'    => $attribute_details->getData('frontend_label'),
                        'attribute_code'    => $attribute_code,
                        'attribute_id'      => $attribute_details->getId(),
                        'position'          => $attribute_details->getData('position'),
                        'store_label'       => $attribute_details->getData('frontend_label'),
                        'use_default'       => '',
                    );

                    /**
                     * configurable attribute data
                     */
                    $configurableAttributesData[] = $configurableAttrData;

                    foreach($options as $option) {
                        $optionData = array('label'=>$option['label'], 'value'=>$option['value']);
                        $data['options'][$option['value']] = $optionData;
                    }
                    $attributeDatas[$attribute_code] = $data;
                }


                //Mage::log($attributeDatas);
                $productData = array();
                $availabelAttributesOption = array();
                foreach($simples_skus as $sku) {
                    $simple_product = Mage::getModel('catalog/product')->loadByAttribute('vendor_sku',$sku);
					if(!$simple_product) continue;
                    foreach($configurable_attributes as $attribute) {
                        $value_index = $simple_product->getData($attribute);
                        $availabelAttributesOption[] = $attribute.'_'.$value_index;
                    }
                    //$productData[$simple_product->getId()] = array();
                    foreach($configurable_attributes as $attribute) {
                        $value = $attributeDatas[$attribute]['attribute_id'];
                        $value_index = $simple_product->getData($attribute);
                        $data = array(
                            'attribute_id'    =>    $value,
                            'label'           =>    $attributeDatas[$attribute]['options'][$value_index]['label'],
                            'value_index'     =>    $value_index,
                        );
                        $productData[$simple_product->getId()][] = $data;
                    }
                }

                //Mage::log($productData);



                //parsing super attribute pricing
                /**
                 * example string size::L:12;XL:15,color::red//yellow:10:1
                 * parse to array('code'=>array('option1'=>array('value'=>'','percent'=>''));
                 */
                $pricing_config = array();
                $attributes_config = explode(',',$super_attribute_pricing); //size::L:12;XL:15
                //color::red//yellow:10:1
                $data2 = array();
                foreach($attributes_config as $attribute_con) {
                    $a = explode('::',$attribute_con);                      //$a[0] = size;$a[1]=L:12;XL:15

                    /**
                     * ignore attributes not in configurable attributes column
                     */
                    if(!in_array($a[0], $configurable_attributes)) continue;

                    //get attribute id of attribute code
                    $attribute_id = $attributeDatas[$a[0]]['attribute_id'];

                    if(count(explode(';',$a[1]))) {
                        //if config for each option
                        $options = explode(';',$a[1]);
                        //$data1 = array();
                        foreach($options as $option) {
                            $option_config = explode(':',$option);
                            $loop = 0;
                            if(count(explode('//',$option_config[0]))) {$loop = 1;}
                            if($loop == 0) {$label = $option_config[0];} elseif($loop == 1) {$label_arr = explode('//',$option_config[0]);$label = $label_arr[0];}
                            if($loop == 0) {
                                $value = $this->_getOptionValueFromLabel($attributeDatas[$a[0]]['options'],$label);
                                $data = array(
                                    'attribute_id'  => $attribute_id,
                                    'value_index'   => $value,
                                    'label'         => $label,
                                    'pricing_value' => $option_config[1],
                                    'is_percent'    => isset($option_config[2])?$option_config[2]:0,
                                );
                                if(in_array($a[0].'_'.$value, $availabelAttributesOption) and is_array($attributeDatas[$a[0]]['options'][$value])) $data2[$attribute_id.'_'.$value] = $data;
                            }
                            elseif($loop == 1) {
                                foreach($label_arr as $label) {
                                    $value = $this->_getOptionValueFromLabel($attributeDatas[$a[0]]['options'],$label);
                                    $data = array(
                                        'attribute_id'  => $attribute_id,
                                        'value_index'   => $value,
                                        'label'         => $label,
                                        'pricing_value' => $option_config[1],
                                        'is_percent'    => isset($option_config[2])?$option_config[2]:'0',
                                    );
                                    if(in_array($a[0].'_'.$value, $availabelAttributesOption) and is_array($attributeDatas[$a[0]]['options'][$value])) $data2[$attribute_id.'_'.$value] = $data;
                                }
                            }
                            //$multi = $option_config[0];if(count(explode('//',$option_config[0]))) {$option_config[] = array()}
                        }
                        //var_dump($data1);
                    }
                    // $data2[] = $data1;

                    /*
                     *
                     *sort order attribute follow configurable_attributes column
                    */

                }

                // $product->getTypeInstance()->setUsedProductAttributeIds(array(272,525)); //attribute ID of attributes
                // $configurableAttributesData = $product->getTypeInstance()->getConfigurableAttributesAsArray();


                // Mage::log($data2);

                /**
                 * prepare attributes configurable data
                 */

                foreach($configurableAttributesData as $id => $configData) {
                    foreach($data2 as $valueData) {
                        if($configData['attribute_id'] == $valueData['attribute_id']) {
                            $configData['values'][] = $valueData;
                        }
                    }
                    $configurableAttributesData[$id] = $configData;
                }

               // Mage::log($configurableAttributesData);


                foreach($productData as $id => $configurableData) {
                    foreach($configurableData as $id2=> $configData){
                        if(count($data2[$configData['attribute_id'].'_'.$configData['value_index']])) {
                            $info = $data2[$configData['attribute_id'].'_'.$configData['value_index']];
                            $configData['pricing_value'] = $info['pricing_value'];
                            $configData['is_percent'] = $info['is_percent'];

                            $configurableData[$id2] = $configData;
                        }
                    }
                    $productData[$id] = $configurableData;
                }

                Mage::log($productData);
                Mage::log($configurableAttributesData);
                $product->setCanSaveConfigurableAttributes(true);
                $product->setCanSaveCustomOptions(true);

                $product->setConfigurableAttributesData($configurableAttributesData);
                $product->setConfigurableProductsData($productData);


               // Mage::log($product->getConfigurableProductsData());
            }
        }


        //end configurable product

        foreach ($this->_ignoreFields as $field) {
            if (isset($importData[$field])) {
                unset($importData[$field]);
            }
        }

        if ($store->getId() != 0) {
            $websiteIds = $product->getWebsiteIds();
            if (!is_array($websiteIds)) {
                $websiteIds = array();
            }
            if (!in_array($store->getWebsiteId(), $websiteIds)) {
                $websiteIds[] = $store->getWebsiteId();
            }
            $product->setWebsiteIds($websiteIds);
        }

        if (isset($importData['websites'])) {
            $websiteIds = $product->getWebsiteIds();
            if (!is_array($websiteIds) || !$store->getId()) {
                $websiteIds = array();
            }
            $websiteCodes = explode(',', $importData['websites']);
            foreach ($websiteCodes as $websiteCode) {
                try {
                    $website = Mage::app()->getWebsite(trim($websiteCode));
                    if (!in_array($website->getId(), $websiteIds)) {
                        $websiteIds[] = $website->getId();
                    }
                } catch (Exception $e) {}
            }
            $product->setWebsiteIds($websiteIds);
            unset($websiteIds);
        }

        foreach ($importData as $field => $value) {
            if(in_array($field, array('media_gallery',"vendor_categories"))) continue;
            
            if (in_array($field, $this->_inventoryFields)) {
                continue;
            }
            if (is_null($value)) {
                continue;
            }

            $attribute = $this->getAttribute($field);
            if (!$attribute) {
                continue;
            }

            $isArray = false;
            $setValue = $value;

            if ($attribute->getFrontendInput() == 'multiselect') {
                $value = explode(self::MULTI_DELIMITER, $value);
                $isArray = true;
                $setValue = array();
            }

            if ($value && $attribute->getBackendType() == 'decimal') {
                $setValue = $this->getNumber($value);
            }

            if ($attribute->usesSource()) {
                $options = $attribute->getSource()->getAllOptions(false);

                if ($isArray) {
                    foreach ($options as $item) {
                        if (in_array($item['label'], $value)) {
                            $setValue[] = $item['value'];
                        }
                    }
                } else {
                    $setValue = false;
                    foreach ($options as $item) {
                        if (is_array($item['value'])) {
                            foreach ($item['value'] as $subValue) {
                                if (isset($subValue['value']) && $subValue['value'] == $value) {
                                    $setValue = $value;
                                }
                            }
                        } else if ($item['label'] == $value) {
                            $setValue = $item['value'];
                        }
                    }
                }
            }

            $product->setData($field, $setValue);
        }

        if (!$product->getVisibility()) {
            $product->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE);
        }

        $stockData = array();
        $inventoryFields = isset($this->_inventoryFieldsProductTypes[$product->getTypeId()])
            ? $this->_inventoryFieldsProductTypes[$product->getTypeId()]
            : array();
        foreach ($inventoryFields as $field) {
            if (isset($importData[$field])) {
                if (in_array($field, $this->_toNumber)) {
                    $stockData[$field] = $this->getNumber($importData[$field]);
                } else {
                    $stockData[$field] = $importData[$field];
                }
            }
        }
        $product->setStockData($stockData);

        if(isset($importData['media_gallery'])){
            $images = explode(";",$importData['media_gallery']);
            foreach($images as $image){
                /*If vendor use an url for the image*/
                if(strpos($image, 'http://') !== false || strpos($image, 'https://')){
                    $file = $this->saveImageToVendorFolder($file,$vendor);
                }
                if (!empty($image) && !$mediaGalleryBackendModel->getImage($product, $vendor->getVendorId()."/media/".$image) && !in_array($image, $addedImages)) {
                    $mediaGalleryBackendModel->addImage($product, Mage::helper('vendorsimport')->getVendorImageFolder($vendor).$image,null,false,false);
                }
            }
        }
        $addedFilesCorrespondence = $mediaGalleryBackendModel->addImagesWithDifferentMediaAttributes(
            $product,
            $arrayToMassAdd, Mage::getBaseDir('media').DS.'ves_vendorsimportproduct'.DS,
            false,
            false
        );
        
        foreach ($product->getMediaAttributes() as $mediaAttributeCode => $mediaAttribute) {
            $addedFile = '';
            if (isset($importData[$mediaAttributeCode . '_label'])) {
                $fileLabel = trim($importData[$mediaAttributeCode . '_label']);
                if (isset($importData[$mediaAttributeCode])) {
                    $keyInAddedFile = array_search($importData[$mediaAttributeCode],
                        $addedFilesCorrespondence['alreadyAddedFiles']);
                    if ($keyInAddedFile !== false) {
                        $addedFile = $addedFilesCorrespondence['alreadyAddedFilesNames'][$keyInAddedFile];
                    }
                }

                if (!$addedFile) {
                    $addedFile = $product->getData($mediaAttributeCode);
                }
                if ($fileLabel && $addedFile) {
                    $mediaGalleryBackendModel->updateImage($product, $addedFile, array('label' => $fileLabel));
                }
            }
        }

        $product->setIsMassupdate(true);
        $product->setExcludeUrlRewrite(true);
        if(!$product->getId()){
            if(Mage::helper('vendorsproduct')->isProductApproval()){
                $product->setData('approval',VES_VendorsProduct_Model_Source_Approval::STATUS_NOT_SUBMITED);
            }else{
                $product->setData('approval',VES_VendorsProduct_Model_Source_Approval::STATUS_APPROVED);
            }
        }

        //Mage::log($product->getConfigurableProductsData());

        $product->save();

        // Store affected products ids
        $this->_addAffectedEntityIds($product->getId());

        return true;
    }

	public function getCategoryIdVendor($pathCategories){
		//var_dump($this->_vendorcategories);exit;
		$categoryIds = array();
        $categoryNameArr = explode(",", $pathCategories);
		foreach($categoryNameArr as $categoryImport){
			$structureImport= explode('/', $categoryImport);
			$pathSizeImport  = count($structureImport);
			if($pathSizeImport == 1){
				$categoryIds[] = $this->_vendorcategories[$categoryImport];
			}
			else{
				$pathI = "";
				for($i = 0 ; $i < $pathSizeImport ; $i++ ){
					$pathI .= $structureImport[$i]."/";
					$categoryIds[] = $this->_vendorcategories[trim($pathI,"/")];
				}
			}
		}
		$result = array_unique($categoryIds);
		
		return implode(",",$result);
	}
	
		 /**
     * Initialize categories text-path to ID hash.
     *
     * @return Mage_ImportExport_Model_Import_Entity_Product
     */
    public function getCategoryIds($pathCategories)
    {
		$categoryIds = array();
        $categoryNameArr = explode(",", $pathCategories);
		foreach($categoryNameArr as $categoryImport){
			$structureImport= explode('/', $categoryImport);
			$pathSizeImport  = count($structureImport);
			if($pathSizeImport == 1){
				$categoryIds[] = $this->_categories[$categoryImport];
			}
			else{
				$pathI = "";
				for($i = 0 ; $i < $pathSizeImport ; $i++ ){
					$pathI .= $structureImport[$i]."/";
					$categoryIds[] = $this->_categories[trim($pathI,"/")];
				}
			}
		}
		
		$result = array_unique($categoryIds);

		return implode(",",$result);
      
    }
	
	public function _initCategoriesVendor($vendor){
	
		$modules = Mage::getConfig()->getNode('modules')->children();
		$modulesArray = (array)$modules;
		if(isset($modules['VES_VendorsGroup']) && isset($modules['VES_VendorsGroup']['active']) && $modules['VES_VendorsGroup']['active']=='true') {
			$collection = Mage::getModel('vendorscategory/category')->getCollection()->addFieldToFilter("vendor_id",$vendor->getId());
			/* @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection */
			foreach ($collection as $category) {
				$structure = explode('/', $category->getPath());
				$pathSize  = count($structure);

				$path = array();
				for ($i = 0; $i < $pathSize; $i++) {
					$categoryOb = Mage::getModel('vendorscategory/category')->load($structure[$i]);
					$path[] = $categoryOb->getName();
				}
				$index = implode('/', $path);
				$this->_vendorcategories[$index] = $category->getId();
			}
		}

        return $this;
	}
	
	public function _initCategories(){
		$collection = Mage::getResourceModel('catalog/category_collection')->addNameToResult();
        /* @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection */
        foreach ($collection as $category) {
            $structure = explode('/', $category->getPath());
            $pathSize  = count($structure);
            if ($pathSize > 1) {
                $path = array();
                for ($i = 1; $i < $pathSize; $i++) {
                    $path[] = $collection->getItemById($structure[$i])->getName();
                }
                $rootCategoryName = array_shift($path);
                if (!isset($this->_categoriesWithRoots[$rootCategoryName])) {
                    $this->_categoriesWithRoots[$rootCategoryName] = array();
                }
                $index = implode('/', $path);
                $this->_categoriesWithRoots[$rootCategoryName][$index] = $category->getId();
                if ($pathSize > 2) {
                    $this->_categories[$index] = $category->getId();
                }
            }
        }
        return $this;
	}
	
    protected function _getOptionValueFromLabel($options,$label) {
        foreach($options as $value => $option) {
            if($option['label'] == $label) return $option['value'];
        }
        return '';
    }
    
    public function saveImageToVendorFolder($imageUrl, VES_Vendors_Model_Vendor $vendor){
        $ch = curl_init($imageUrl);
        $filename = explode("/", $imageUrl);
        $filename = end($filename);
        
        $fp = fopen(Mage::helper('vendorsimport')->getVendorImageFolder($vendor).$filename, 'wb');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        
        return $filename;
    }
}