<?php

class VES_VendorsProductImage_Helper_Data extends Mage_Core_Helper_Abstract
{
    
    public function isAjax(){
        return Mage::getStoreConfig('vendors/catalog/use_ajax_image') ? true : false;
    }
    
}