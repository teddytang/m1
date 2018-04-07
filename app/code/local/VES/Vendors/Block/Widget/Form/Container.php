<?php
/**
 * Vendors page breadcrumbs
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_Vendors_Block_Widget_Form_Container extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
    {
        parent::__construct();
        $this->_updateButton('reset', 'class', 'reset');
    }
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
