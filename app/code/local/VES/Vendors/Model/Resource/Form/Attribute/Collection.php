<?php

/**
 * Vendor Form Attribute Resource Collection
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_Vendors_Model_Resource_Form_Attribute_Collection extends Mage_Eav_Model_Resource_Form_Attribute_Collection
{
    /**
     * Current module pathname
     *
     * @var string
     */
    protected $_moduleName = 'vendors';

    /**
     * Current EAV entity type code
     *
     * @var string
     */
    protected $_entityTypeCode = 'ves_vendor';

    /**
     * Resource initialization
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('eav/attribute');
    }

    /**
     * Get EAV website table
     *
     * Get table, where website-dependent attribute parameters are stored.
     * If realization doesn't demand this functionality, let this function just return null
     *
     * @return string|null
     */
    protected function _getEavWebsiteTable()
    {
        return $this;
    }
}
