<?php
/**
 * Vendor form element
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <core@magentocommerce.com>
 */
class VES_Vendors_Block_Form_Element_Status extends Varien_Data_Form_Element_Label
{
    /**
     * Assigns attributes for Element
     *
     * @param array $attributes
     */
    public function __construct($attributes=array())
    {
        parent::__construct($attributes);
        $this->setType('label');
    }

    /**
     * Retrieve Element HTML
     *
     * @return string
     */
    public function getElementHtml()
    {
    	$values = $this->getValues();
    	$valueText = '';
    	foreach ($values as $value){
    		if($value['value'] == $this->getEscapedValue()) $valueText = $value['label'];
    	}
        $html = $this->getBold() ? '<strong>' : '';
        $html.= '<span class="status '.strtolower($valueText).'">'.$valueText.'</span>';
        $html.= $this->getBold() ? '</strong>' : '';
        $html.= $this->getAfterElementHtml();
        return $html;
    }
}
