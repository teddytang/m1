<?php

class VES_VendorsImage_Vendor_Cms_WysiwygController extends VES_Vendors_Controller_Action
{
    /**
     * Template directives callback
     *
     * TODO: move this to some model
     */
    public function directiveAction()
    {
        $directive = $this->getRequest()->getParam('___directive');
        $directive = Mage::helper('core')->urlDecode($directive);
     
        $url = Mage::getModel('cms/adminhtml_template_filter')->filter($directive);
     
        try {
            $image = Varien_Image_Adapter::factory('GD2');
            $image->open($url);
            $image->display();
        } catch (Exception $e) {
            $image = Varien_Image_Adapter::factory('GD2');
            $image->open(Mage::getSingleton('vendorsimage/wysiwyg_config')->getSkinImagePlaceholderPath());
            $image->display();
            /*
            $image = imagecreate(100, 100);
            $bkgrColor = imagecolorallocate($image,10,10,10);
            imagefill($image,0,0,$bkgrColor);
            $textColor = imagecolorallocate($image,255,255,255);
            imagestring($image, 4, 10, 10, 'Skin image', $textColor);
            header('Content-type: image/png');
            imagepng($image);
            imagedestroy($image);
            */
        }
    }
}
