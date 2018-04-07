<?php

class VES_VendorsImportProduct_Model_Convert_Parser_Product extends Mage_Catalog_Model_Convert_Parser_Product
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
	
	public function __construct()
    {
    	parent::__construct();
    	foreach (Mage::getConfig()->getNode('admin/import_product/ignore_fields')->asArray() as $code=>$node) {
	    	$this->_systemFields[] = $code;
    	}
    	
    	$notExportedAttributes = explode(",",Mage::getStoreConfig('vendors/vendors_import_export/ignored_attributes'));
    	foreach($notExportedAttributes as $attr){
    		$this->_systemFields[] = $attr;
    	}
    	//foreach()
    }
    
	public function getVendor(){
		return Mage::getSingleton('vendors/session')->getVendor();;
	}
    
	
	/**
     * Unparse (prepare data) loaded products
     *
     * @return Mage_Catalog_Model_Convert_Parser_Product
     */
    public function unparse()
    {
		
		$vendor = $this->getVendor();
		$this->_initCategories();

        $entityIds = $this->getData();

        foreach ($entityIds as $i => $entityId) {
            $product = Mage::getModel("catalog/product")
                ->setStoreId($this->getStoreId())
                ->load($entityId);
            $this->setProductTypeInstance($product);
            /* @var $product Mage_Catalog_Model_Product */

            $position = Mage::helper('catalog')->__('Line %d, SKU: %s', ($i+1), $product->getSku());
            $this->setPosition($position);

            $row = array(
                'store'         => $this->getStore()->getCode(),
                'websites'      => '',
                'attribute_set' => $this->getAttributeSetName($product->getEntityTypeId(),
                                        $product->getAttributeSetId()),
                'type'          => $product->getTypeId(),
                /*'category_ids'  => join(',', $product->getCategoryIds())*/
            );

            $categories = $product->getCategoryIds();
            $catNames = array();
            
			
			$categories_ids= $this->getPathCategoryIds($categories);
			$row['categories'] = $categories_ids;
			//var_dump($categories_ids);exit;
			
           // $categoryCollection = Mage::getModel('catalog/category')->getCollection()->addAttributeToSelect('name')->addAttributeToFilter('entity_id',array('in'=>$categories));
           // foreach($categoryCollection as $category){
            //    $catNames[] = $category->getName();
         //   }
          //  $row['categories'] = implode(',', $catNames);
			
			

            if ($this->getStore()->getCode() == Mage_Core_Model_Store::ADMIN_CODE) {
                $websiteCodes = array();
                foreach ($product->getWebsiteIds() as $websiteId) {
                    $websiteCode = Mage::app()->getWebsite($websiteId)->getCode();
                    $websiteCodes[$websiteCode] = $websiteCode;
                }
                $row['websites'] = join(',', $websiteCodes);
            } else {
                $row['websites'] = $this->getStore()->getWebsite()->getCode();
                if ($this->getVar('url_field')) {
                    $row['url'] = $product->getProductUrl(false);
                }
            }
			
					
			if($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE){
				$childProducts = Mage::getModel('catalog/product_type_configurable')
                    ->getUsedProducts(null,$product);
				$simple_skus = array();
				foreach($childProducts as $child) {
					$simple_skus[] = $child->getSku();
				}
				if(!$row['simples_skus'])
				$row['simples_skus'] = implode(",",$simple_skus);
				
				$config = $product->getTypeInstance(true);
                //loop through the attributes
				$attributeCf = array();
				$super_attribute_pricing = array();
                foreach($config->getConfigurableAttributesAsArray($product) as $attributes)
                {
					$attributeCf[] = $attributes['attribute_code'];
					$dataValue = array();
					foreach($attributes['values'] as $value){
						$dataValue[] = $value["label"].":".$value["pricing_value"];
					}
					$super_attribute_pricing[] = $attributes['attribute_code']."::".implode(";",$dataValue);
				}
				if(!$row['configurable_attributes'])
				$row['configurable_attributes'] = implode(",",$attributeCf);
				if(!$row['super_attribute_pricing'])
				$row['super_attribute_pricing'] = implode(",",$super_attribute_pricing);
			}
			else{
				$row['simples_skus'] = "";
				$row['configurable_attributes'] = "";
				$row['super_attribute_pricing'] = "";
			}
			
			
            foreach ($product->getData() as $field => $value) {
                if (in_array($field, $this->_systemFields) || is_object($value)) {
                    continue;
                }
				
                $attribute = $this->getAttribute($field);
                if (!$attribute) {
                    continue;
                }

				
				
				
                if ($attribute->usesSource()) {
					$modules = Mage::getConfig()->getNode('modules')->children();
					$modulesArray = (array)$modules;

					/**for vendor categories */
					if(isset($modules['VES_VendorsGroup']) && isset($modules['VES_VendorsGroup']['active']) && $modules['VES_VendorsGroup']['active']=='true') {
						$this->_initCategoriesVendor($this->getVendor());
	
						if($attribute->getAttributeCode() == 'vendor_categories') {
							$value = trim($value,',');
							$vendor_categories = explode(',',$value);
							$label = $this->getVendorPathCategoryIds($vendor_categories);
							/*
							$value = trim($value,',');
							$vendor_categories = explode(',',$value);
							$source = Mage::getModel('vendorscategory/source_category')->toArray($this->getVendor()->getId());
							$label = '';
							foreach($vendor_categories as $count => $id) {
								$label .= $source[$id];
								if($count < count($vendor_categories)-1) $label.=',';
							}
							*/
							$row[$field] = $label;
							
							continue;
							
						}
					}
					
                    $option = $attribute->getSource()->getOptionText($value);
                    if ($value && empty($option) && $option != '0') {
                        $this->addException(
                            Mage::helper('catalog')->__('Invalid option ID specified for %s (%s), skipping the record.', $field, $value),
                            Mage_Dataflow_Model_Convert_Exception::ERROR
                        );
                        continue;
                    }
                    if (is_array($option)) {
                        $value = join(self::MULTI_DELIMITER, $option);
                    } else {
                        $value = $option;
                    }
                    unset($option);
                } elseif (is_array($value)) {
                    continue;
                }
				
                /*Vendor should not need to know about sku he will be able to edit the vendor_sku only*/
                if($field=='vendor_sku') $field = 'sku';
                
                $row[$field] = $value;
            }

            if ($stockItem = $product->getStockItem()) {
                foreach ($stockItem->getData() as $field => $value) {
                    if (in_array($field, $this->_systemFields) || is_object($value)) {
                        continue;
                    }
                    $row[$field] = $value;
                }
            }

            foreach ($this->_imageFields as $field) {
                if (isset($row[$field]) && $row[$field] == 'no_selection') {
                    $row[$field] = null;
                }
            }

            $batchExport = $this->getBatchExportModel()
                ->setId(null)
                ->setBatchId($this->getBatchModel()->getId())
                ->setBatchData($row)
                ->setStatus(1)
                ->save();
            $product->reset();
        }

        return $this;
    }
	
	public function getVendorPathCategoryIds($categoryIds){
		$paths = array();
		foreach($categoryIds as $categoryId){
			$paths[] =  $this->_vendorcategories[$categoryId];
		}
		$pathNews = array();
		foreach($paths as $path){
			$tmp = str_replace("/",";",$path);
			$check = true;
			foreach($paths as $pathcheck){
				$tmp1 = str_replace("/",";",$pathcheck);
				if($tmp == $tmp1) continue;
				if(preg_match("/".$tmp."/s",$tmp1,$math)){
					$check = false;
				}
			}
			if($check) $pathNews[] = $path;
		}
		$result = array_unique($pathNews);
		return implode(",",$result);
	}
	
		 /**
     * Initialize categories text-path to ID hash.
     *
     * @return Mage_ImportExport_Model_Import_Entity_Product
     */
    public function getPathCategoryIds($categoryIds)
    {
		$paths = array();
		foreach($categoryIds as $categoryId){
			$paths[] =  $this->_categories[$categoryId];
		}
		$pathNews = array();
		foreach($paths as $path){
			$tmp = str_replace("/",";",$path);
			$check = true;
			foreach($paths as $pathcheck){
				$tmp1 = str_replace("/",";",$pathcheck);
				if($tmp == $tmp1) continue;
				if(preg_match("/".$tmp."/s",$tmp1,$math)){
					$check = false;
				}
			}
			if($check) $pathNews[] = $path;
		}
		$result = array_unique($pathNews);
		return implode(",",$result);
    }
	
	public function _initCategoriesVendor($vendor){
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
			$this->_vendorcategories[$category->getId()] = $index;
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
				
				$this->_categoriesWithRoots[$category->getId()] = array_shift($path);
                if ($pathSize > 2) {
                    $this->_categories[$category->getId()] = implode('/', $path);
                }
            }
        }
        return $this;
	}
	
	
}