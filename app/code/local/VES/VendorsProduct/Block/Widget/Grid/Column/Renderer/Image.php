<?php

/**
 * Adminhtml grid item renderer
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */

class VES_VendorsProduct_Block_Widget_Grid_Column_Renderer_Image
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Options
{
	/**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $product 	= $row->load($row->getId());
        $baseDir 	= Mage::getSingleton('catalog/product_media_config')->getBaseMediaPath();
        $imageFile 	= $baseDir.$product->getSmallImage();
        $vendor 	= $this->getVendor();
        if(!$vendor) throw new VES_Vendors_Exception($this->__('Vendor is not set for this renderer.'));
        $width 		= Mage::helper('vendorsconfig')->getVendorConfig('catalog/product/thumbnail_with',$vendor->getId());
        $height 	= Mage::helper('vendorsconfig')->getVendorConfig('catalog/product/thumbnail_height',$vendor->getId());
        
        if(file_exists($imageFile) && is_file($imageFile)){
        	$src = Mage::helper('catalog/image')->init($product, 'small_image')->resize($width,$height);
        }else{
        	$src = $this->getSkinUrl('ves_vendors/images/catalog/product/placeholder/small_image.jpg');
        }
        return '<img src="'.$src.'" width="'.$width.'" height="'.$height.'"/>';
    }
}
