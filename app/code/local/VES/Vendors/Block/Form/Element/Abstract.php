<?php
/**
 * Vendor form element
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <core@magentocommerce.com>
 */
class VES_Vendors_Block_Form_Element_Abstract extends Varien_Data_Form_Element_Abstract
{

	public function getHtmlId()
    {
        return $this->getData('id');
    }
	public function getName()
    {
        return $this->getData('name');
    }
}
