<?php

class VES_VendorsImportProduct_Model_Catalog_Product_Attribute_Backend_Media extends Mage_Catalog_Model_Product_Attribute_Backend_Media
{
	protected $_vendor;
	public function setVendor(VES_Vendors_Model_Vendor $vendor){
		$this->_vendor = $vendor;
	}
	
	public function getVendor(){
		return $this->_vendor;
	}
	
	/**
     * Add image to media gallery and return new filename
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string                     $file              file path of image in file system
     * @param string|array               $mediaAttribute    code of attribute with type 'media_image',
     *                                                      leave blank if image should be only in gallery
     * @param boolean                    $move              if true, it will move source file
     * @param boolean                    $exclude           mark image as disabled in product page view
     * @return string
     */
    public function addImage(Mage_Catalog_Model_Product $product, $file,
        $mediaAttribute = null, $move = false, $exclude = true)
    {
        $file = realpath($file);
        if (!$file || !file_exists($file)) {
            Mage::throwException(Mage::helper('catalog')->__('Image does not exist. (SKU: %s) %s',$product->getSku(),$file));
        }

        Mage::dispatchEvent('catalog_product_media_add_image', array('product' => $product, 'image' => $file));

        $pathinfo = pathinfo($file);
        $imgExtensions = array('jpg','jpeg','gif','png');
        if (!isset($pathinfo['extension']) || !in_array(strtolower($pathinfo['extension']), $imgExtensions)) {
            Mage::throwException(Mage::helper('catalog')->__('Invalid image file type. (SKU: %s)',$product->getSku()));
        }

        $fileName       = Mage_Core_Model_File_Uploader::getCorrectFileName($pathinfo['basename']);
        $dispretionPath = $this->getVendor()->getVendorId().DS.'media'; /*Mage_Core_Model_File_Uploader::getDispretionPath($fileName);*/
        $fileName       = $dispretionPath . DS . $fileName;

        //$fileName = $this->_getNotDuplicatedFilename($fileName, $dispretionPath);

        $ioAdapter = new Varien_Io_File();
        $ioAdapter->setAllowCreateFolders(true);
        $distanationDirectory = dirname($this->_getConfig()->getTmpMediaPath($fileName));

        try {
            $ioAdapter->open(array(
                'path'=>$distanationDirectory
            ));

            /** @var $storageHelper Mage_Core_Helper_File_Storage_Database */
            $storageHelper = Mage::helper('core/file_storage_database');
            if ($move) {
                $ioAdapter->mv($file, $this->_getConfig()->getTmpMediaPath($fileName));

                //If this is used, filesystem should be configured properly
                $storageHelper->saveFile($this->_getConfig()->getTmpMediaShortUrl($fileName));
            } else {
                $ioAdapter->cp($file, $this->_getConfig()->getTmpMediaPath($fileName));

                $storageHelper->saveFile($this->_getConfig()->getTmpMediaShortUrl($fileName));
                $ioAdapter->chmod($this->_getConfig()->getTmpMediaPath($fileName), 0777);
            }
        }
        catch (Exception $e) {
            Mage::throwException(Mage::helper('catalog')->__('Failed to move file: %s', $e->getMessage()));
        }

        $fileName = str_replace(DS, '/', $fileName);

        $attrCode = $this->getAttribute()->getAttributeCode();
        $mediaGalleryData = $product->getData($attrCode);
        $position = 0;
        if (!is_array($mediaGalleryData)) {
            $mediaGalleryData = array(
                'images' => array()
            );
        }

        foreach ($mediaGalleryData['images'] as &$image) {
            if (isset($image['position']) && $image['position'] > $position) {
                $position = $image['position'];
            }
        }

        $position++;
        $mediaGalleryData['images'][] = array(
            'file'     => $fileName,
            'position' => $position,
            'label'    => '',
            'disabled' => (int) $exclude
        );

        $product->setData($attrCode, $mediaGalleryData);

        if (!is_null($mediaAttribute)) {
            $this->setMediaAttribute($product, $mediaAttribute, $fileName);
        }

        return $fileName;
    }
    
    /**
     * Retrive image from gallery
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string $file
     * @return array|boolean
     */
    public function getImage(Mage_Catalog_Model_Product $product, $file)
    {
        
        $attrCode = $this->getAttribute()->getAttributeCode();
        $mediaGalleryData = $product->getData($attrCode);

        if (!isset($mediaGalleryData['images']) || !is_array($mediaGalleryData['images'])) {
            return false;
        }
        foreach ($mediaGalleryData['images'] as $image) {
            if ($image['file'] == $file) {
                return $image;
            }
        }

        return false;
    }
}