<?php

class VES_VendorsProductImage_Model_Observer
{
    public function onCatalogProductPrepareSave($observer)
    {
        $product = $observer->getProduct();
        $request = $observer->getRequest();

        $imgUploadData = $request->getParam('vesimgupload');

        $productData   = $request->getParam('product');
        $newImages = $request->getParam('vesimgupload_new');
        if (!$product->getId() && $newImages)
        {
            $this->addMediaImages($product, $newImages);
        }
        $this->saveMediaGallery($product, $imgUploadData,$newImages, $productData);
    }

    public function addMediaImages($product, $imageData)
    {
        if (is_array($imageData) && !empty($imageData))
        {
            $mediaGallery = $product->getMediaGallery();
            	
            foreach ($imageData as $file => $url)
            {
                $mediaGallery['images'][] = array(
                    'file'  => $file,
                    'url'   => $url,
                    'disabled' => 0,
                    'removed' => 0,
                    'position' => count($mediaGallery['images']) + 1,
                );
            }
            
            $product->setMediaGallery($mediaGallery);
        }
    }

    public function saveMediaGallery($product, $imgUploadData,$newImages, $productData)
    {
        
       
        if (!$imgUploadData || !$productData)
        {
            return false;
        }

        $entityTypeId = Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId();
        $mediaGalleryAttribute = Mage::getModel('catalog/resource_eav_attribute')->loadByCode($entityTypeId, 'media_gallery');

        $mediaGallery = $product->getMediaGallery();
        if (isset($mediaGallery['images']) && $mediaGallery['images'])
        {
            if (!is_array($mediaGallery['images']))
            {
                $mediaImages = Zend_Json::decode($mediaGallery['images']);
            } else
            {
                $mediaImages = $mediaGallery['images'];
            }
            
            if (is_array($mediaImages) && !empty($mediaImages))
            {
                foreach ($mediaImages as &$image)
                {
                    // applying disabled values
                    if (isset($imgUploadData['disable']) && is_array($imgUploadData['disable']) && !empty($imgUploadData['disable']))
                    {
                        foreach ($imgUploadData['disable'] as $file => $disabled)
                        {
                            if ($image['file'] == $file)
                            {
                                $image['disabled']         = $disabled;
                                $image['disabled_default'] = $disabled;
                            }
                        }
                    }

                    // removing images if any
                    if (isset($imgUploadData['delete']) && is_array($imgUploadData['delete']) && !empty($imgUploadData['delete']))
                    {
                        foreach ($imgUploadData['delete'] as $file => $delete)
                        {
                            if ($image['file'] == $file)
                            {
                                if ($delete)
                                {//echo $image['file'];exit;
                                    $image['removed'] = 1;
                                    @unlink(Mage::getBaseDir('media') . "/catalog/product" . $image['file']);
                                } elseif(isset($image['removed']))
                                {
                                    unset($image['removed']);
                                }
                            }
                        }
                    }

                    // applying labels
                    if (isset($imgUploadData['label']) && is_array($imgUploadData['label']) && !empty($imgUploadData['label']))
                    {
                        foreach ($imgUploadData['label'] as $file => $label)
                        {
                            if ($image['file'] == $file)
                            {
                                $image['label'] = $label;
                                $image['label_default'] = $label;
                            }
                        }
                    }

                    // applying positions
                    if (isset($imgUploadData['position']) && is_array($imgUploadData['position']) && !empty($imgUploadData['position']))
                    {
                        foreach ($imgUploadData['position'] as $file => $position)
                        {
                            if ($image['file'] == $file)
                            {
                                $image['position'] = $position;
                                $image['position_default'] = $position;
                            }
                        }
                    }
                }
            }
            
            
            if (is_array($newImages) && !empty($newImages) && $product->getId())
            {
                
                foreach ($newImages as $file => $url)
                {
                    $imageNew = array(
                        'file'  => $file,
                        'url'   => $url,
                        'disabled' => 0,
                        'removed' => 0,
                        'position' => count($mediaImages) + 1,
                    );
                    if (isset($imgUploadData['disable']) && is_array($imgUploadData['disable']) && !empty($imgUploadData['disable']))
                    {
                        foreach ($imgUploadData['disable'] as $file => $disabled)
                        {
                            if ($imageNew['file'] == $file)
                            {
                                $imageNew['disabled']         = $disabled;
                                $imageNew['disabled_default'] = $disabled;
                            }
                        }
                    }
                    
                    // removing images if any
                    if (isset($imgUploadData['delete']) && is_array($imgUploadData['delete']) && !empty($imgUploadData['delete']))
                    {
                        foreach ($imgUploadData['delete'] as $file => $delete)
                        {
                            if ($imageNew['file'] == $file)
                            {
                                if ($delete)
                                {//echo $image['file'];exit;
                                    $imageNew['removed'] = 1;
                                    @unlink(Mage::getBaseDir('media') . "/catalog/product" . $imageNew['file']);
                                } elseif(isset($imageNew['removed']))
                                {
                                    unset($imageNew['removed']);
                                }
                            }
                        }
                    }
                    
                    // applying labels
                    if (isset($imgUploadData['label']) && is_array($imgUploadData['label']) && !empty($imgUploadData['label']))
                    {
                        foreach ($imgUploadData['label'] as $file => $label)
                        {
                            if ($imageNew['file'] == $file)
                            {
                                $imageNew['label'] = $label;
                                $imageNew['label_default'] = $label;
                            }
                        }
                    }
                    
                    // applying positions
                    if (isset($imgUploadData['position']) && is_array($imgUploadData['position']) && !empty($imgUploadData['position']))
                    {
                        foreach ($imgUploadData['position'] as $file => $position)
                        {
                            if ($imageNew['file'] == $file)
                            {
                                $imageNew['position'] = $position;
                                $imageNew['position_default'] = $position;
                            }
                        }
                    }
                    
                    $mediaImages[] = $imageNew;
                }
            
            }
            
            
            $mediaGallery['images'] = Zend_Json::encode($mediaImages);
        }else{
            if (is_array($newImages) && !empty($newImages) && $product->getId())
            {
                 
                foreach ($newImages as $file => $url)
                {
                    $imageNew = array(
                        'file'  => $file,
                        'url'   => $url,
                        'disabled' => 0,
                        'removed' => 0,
                        'position' => count($mediaImages) + 1,
                    );
                    // applying disabled values
                    if (isset($imgUploadData['disable']) && is_array($imgUploadData['disable']) && !empty($imgUploadData['disable']))
                    {
                        foreach ($imgUploadData['disable'] as $file => $disabled)
                        {
                            if ($imageNew['file'] == $file)
                            {
                                $imageNew['disabled']         = $disabled;
                                $imageNew['disabled_default'] = $disabled;
                            }
                        }
                    }
                    
                    // removing images if any
                    if (isset($imgUploadData['delete']) && is_array($imgUploadData['delete']) && !empty($imgUploadData['delete']))
                    {
                        foreach ($imgUploadData['delete'] as $file => $delete)
                        {
                            if ($imageNew['file'] == $file)
                            {
                                if ($delete)
                                {//echo $image['file'];exit;
                                    $imageNew['removed'] = 1;
                                    @unlink(Mage::getBaseDir('media') . "/catalog/product" . $imageNew['file']);
                                } elseif(isset($imageNew['removed']))
                                {
                                    unset($imageNew['removed']);
                                }
                            }
                        }
                    }
                    
                    // applying labels
                    if (isset($imgUploadData['label']) && is_array($imgUploadData['label']) && !empty($imgUploadData['label']))
                    {
                        foreach ($imgUploadData['label'] as $file => $label)
                        {
                            if ($imageNew['file'] == $file)
                            {
                                $imageNew['label'] = $label;
                                $imageNew['label_default'] = $label;
                            }
                        }
                    }
                    
                    // applying positions
                    if (isset($imgUploadData['position']) && is_array($imgUploadData['position']) && !empty($imgUploadData['position']))
                    {
                        foreach ($imgUploadData['position'] as $file => $position)
                        {
                            if ($imageNew['file'] == $file)
                            {
                                $imageNew['position'] = $position;
                                $imageNew['position_default'] = $position;
                            }
                        }
                    }
                    
                    $mediaImages[] = $imageNew;
                }
                $mediaGallery['images'] = Zend_Json::encode($mediaImages);
            }
        }

        
        if (isset($mediaGallery['values']) && $mediaGallery['values'])
        {
            if (!is_array($mediaGallery['values']))
            {
                $mediaValues = Zend_Json::decode($mediaGallery['values']);
            } else
            {
                $mediaValues = $mediaGallery['values'];
            }
            $mediaValues['image']       = $productData['image'];
            $mediaValues['small_image'] = $productData['small_image'];
            $mediaValues['thumbnail']   = $productData['thumbnail'];
            $mediaGallery['values'] = Zend_Json::encode($mediaValues);
        }

        if (isset($productData['image'])) {
            $product->setImage($productData['image']);
        }
        if (isset($productData['small_image'])) {
            $product->setSmallImage($productData['small_image']);
        }
        if (isset($productData['thumbnail'])) {
            $product->setThumbnail($productData['thumbnail']);
        }

        $product->setMediaGallery($mediaGallery);
    }
    
    public function adminhtml_catalog_product_edit_element_types(Varien_Event_Observer $observer){
        $response = $observer->getResponse();
        $types = $response->getTypes();
        
        $types['gallery'] = 'VES_VendorsProductImage_Block_Catalog_Product_Helper_Form_Gallery';
        $response->setTypes($types);
    }
}