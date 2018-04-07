<?php

/**
 * Vendor password attribute backend
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_Vendors_Model_Vendor_Attribute_Backend_File extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Special processing before attribute save:
     * a) check some rules for password
     * b) transform temporary attribute 'password' into real attribute 'password_hash'
     */
    public function beforeSave($object)
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        
        if(isset($_FILES[$attributeCode]['name']) && $_FILES[$attributeCode]['name'] != '') {
			if(!file_exists($_FILES[$attributeCode]['tmp_name'])) return;
            /* Starting upload */
            $uploader = new Varien_File_Uploader($attributeCode);
            $allowedExtensions = str_replace(" ", "", $this->getAttribute()->getDefaultValue());
            $allowedExtensions = explode(",",$allowedExtensions);
            $uploader->setAllowedExtensions($allowedExtensions);
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            $path = Mage::getBaseDir('media') . DS."ves_vendors".DS.$attributeCode.DS ;
            $uploader->save($path, $_FILES[$attributeCode]['name']);
            $object->setData($attributeCode,"ves_vendors/".$attributeCode.$uploader->getUploadedFileName());

        }else{
            $data = $object->getData($attributeCode);
            if(isset($data['delete']) && $data['delete']){
                $object->setData($attributeCode,'');
                @unlink(Mage::getBaseDir('media').DS.str_replace("/", DS, $data['value']));
            }else{
                //$object->setData($attributeCode,$data['value']);
            }
        }
    }

    public function validate($object)
    {
        return parent::validate($object);
    }

}
