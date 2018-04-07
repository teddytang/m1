<?php

/**
 * Vendor password attribute backend
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_Vendors_Model_Vendor_Attribute_Backend_Password extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Special processing before attribute save:
     * a) check some rules for password
     * b) transform temporary attribute 'password' into real attribute 'password_hash'
     */
    public function beforeSave($object)
    {
        $password = trim($object->getPassword());
        $len = Mage::helper('core/string')->strlen($password);
        if ($len) {
             if ($len < 6) {
                Mage::throwException(Mage::helper('customer')->__('The password must have at least 6 characters. Leading or trailing spaces will be ignored.'));
            }
            $object->setPasswordHash($object->hashPassword($password));
        }
    }

    public function validate($object)
    {
        if ($password = $object->getPassword()) {
            if ($password == $object->getPasswordConfirm()) {
                return true;
            }
        }

        return parent::validate($object);
    }

}
