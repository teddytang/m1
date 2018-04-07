<?php
class VES_VendorsProduct_Model_Catalog_Product_Url extends Mage_Catalog_Model_Product_Url
{
	/**
     * Returns checked store_id value
     *
     * @param int|null $id
     * @return int
     */
    protected function _getStoreId($id = null)
    {
        return Mage::app()->getStore($id)->getId();
    }
	/**
     * Retrieve product URL based on requestPath param
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string $requestPath
     * @param array $routeParams
     *
     * @return string
     */
    protected function _getProductUrl($product, $requestPath, $routeParams)
    {
    	if (!empty($requestPath)) {
            return $this->getUrlInstance()->getDirectUrl($requestPath, $routeParams);
        }
        $routeParams['id'] = $product->getId();
        $routeParams['s'] = $product->getUrlKey();
        if($vendor = Mage::registry('vendor') ||($product->getData('can_display_vendor_product_url') && $product->getVendorId()) || $product->getForceShowVendorUrl()){
        	$vendorId = Mage::registry('vendor')?Mage::registry('vendor')->getVendorId():Mage::getModel('vendors/vendor')->load($product->getVendorId())->getVendorId();
        	$baseUrlKey = Mage::getStoreConfig('vendors/vendor_page/url_key');
        	if($baseUrlKey){
        		$url = $this->getUrlInstance()->getUrl($baseUrlKey.'/'.$vendorId.'/product').'view';
        	}else{
        		$url = $this->getUrlInstance()->getUrl($vendorId.'/product').'view';
        	}
        	foreach($routeParams as $key=>$value){
        		if(is_array($value)) continue;
        		$url.='/'.$key.'/'.$value;
        	}
        	return $url;
        }
        $categoryId = $this->_getCategoryIdForUrl($product, $routeParams);
        if ($categoryId) {
            $routeParams['category'] = $categoryId;
        }
        return $this->getUrlInstance()->getUrl('catalog/product/view', $routeParams);
    }
	/**
     * Check product category
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $params
     *
     * @return int|null
     */
    protected function _getCategoryIdForUrl($product, $params)
    {
        if (isset($params['_ignore_category'])) {
            return null;
        } else {
            return $product->getCategoryId() && !$product->getDoNotUseCategoryId()
                ? $product->getCategoryId() : null;
        }
    }
    
	/**
     * Retrieve request path
     *
     * @param Mage_Catalog_Model_Product $product
     * @param int $categoryId
     * @return bool|string
     */
    protected function _getRequestPath($product, $categoryId)
    {
    	$canDisplayVendorProductUrl = new Varien_Object(array('flag'=>false));
    	Mage::dispatchEvent('ves_vendorsproduct_prepare_url',array('can_display_vendor_product_url'=>$canDisplayVendorProductUrl,'product'=>$product));
    	$flag = ($vendor = Mage::registry('vendor')) || ($canDisplayVendorProductUrl->getFlag() && $product->getVendorId() || $product->getForceShowVendorUrl());
    	if($flag){
    		$product->setData('can_display_vendor_product_url',$flag);
    		$vendorId = Mage::registry('vendor')?Mage::registry('vendor')->getVendorId():Mage::getModel('vendors/vendor')->load($product->getVendorId())->getVendorId();
    		$idPath = sprintf('%s/product/%d', $vendorId,$product->getEntityId());
    	}else{
	        $idPath = sprintf('product/%d', $product->getEntityId());
	        if ($categoryId) {
	            $idPath = sprintf('%s/%d', $idPath, $categoryId);
	        }
    	}
        $rewrite = $this->getUrlRewrite();
        $rewrite->setStoreId($product->getStoreId())
            ->loadByIdPath($idPath);
        if ($rewrite->getId()) {
            return $rewrite->getRequestPath();
        }

        return false;
    }
    
	/**
     * Retrieve Product URL using UrlDataObject
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $params
     * @return string
     */
    public function getUrl(Mage_Catalog_Model_Product $product, $params = array())
    {
        $url = $product->getData('url');
        if (!empty($url)) {
            return $url;
        }
   		if(isset($params['_vendor']) && $params['_vendor']){
        	$product->setForceShowVendorUrl(true);
        }
        
        $requestPath = $product->getData('request_path');
        //if (empty($requestPath)) {
            $requestPath = $this->_getRequestPath($product, $this->_getCategoryIdForUrl($product, $params));
            $product->setRequestPath($requestPath);
        //}

        if (isset($params['_store'])) {
            $storeId = $this->_getStoreId($params['_store']);
        } else {
            $storeId = $product->getStoreId();
        }

        if ($storeId != $this->_getStoreId()) {
            $params['_store_to_url'] = true;
        }

        // reset cached URL instance GET query params
        if (!isset($params['_query'])) {
            $params['_query'] = array();
        }
		
        $this->getUrlInstance()->setStore($storeId);
        $productUrl = $this->_getProductUrl($product, $requestPath, $params);
        $product->setData('url', $productUrl);
        return $product->getData('url');
    }
}
