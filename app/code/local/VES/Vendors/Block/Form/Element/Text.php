<?php
/**
 * Vendor form element
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <core@magentocommerce.com>
 */
class VES_Vendors_Block_Form_Element_Text extends VES_Vendors_Block_Form_Element_Abstract
{
	public function __construct($attributes=array())
    {
        parent::__construct($attributes);
        $this->setType('text');
        $this->setExtType('textfield');
    }

    public function getHtml()
    {
        $this->addClass('input-text');
        return parent::getHtml();
    }

    public function getHtmlAttributes()
    {
        return array('type', 'title', 'class', 'style', 'onclick', 'onchange', 'onkeyup', 'disabled', 'readonly', 'maxlength', 'tabindex');
    }
    
	public function getHtmlId()
    {
        return $this->getData('id');
    }
	public function getName()
    {
        return $this->getData('name');
    }
}
