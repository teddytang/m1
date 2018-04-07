<?php
/**
 * Vendors page breadcrumbs
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_Vendors_Block_Widget_Form extends Mage_Adminhtml_Block_Widget_Form
{
	/**
     * Returns url model class name
     *
     * @return string
     */
    protected function _getUrlModelClass()
    {
        return 'core/url';
    }
}
