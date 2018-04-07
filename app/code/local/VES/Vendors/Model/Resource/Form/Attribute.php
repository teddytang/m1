<?php
/**
 * Vendor Form Attribute Resource Model
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_Vendors_Model_Resource_Form_Attribute extends Mage_Eav_Model_Resource_Form_Attribute
{
    /**
     * Initialize connection and define main table
     *
     */
    protected function _construct()
    {
        $this->_init('vendors/form_attribute', 'attribute_id');
    }
}
