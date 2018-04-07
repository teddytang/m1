<?php
/**
 * Vendor form element
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <core@magentocommerce.com>
 */
class VES_Vendors_Block_Form_Element_Yesno extends VES_Vendors_Block_Form_Element_Abstract
{
	public function __construct($attributes=array())
    {
        parent::__construct($attributes);
        $this->setType('select');
        $this->setExtType('boolean');
        $this->_prepareOptions();
    }

    public function getElementHtml()
    {
        $this->addClass('select');
        $html = '<select id="'.$this->getHtmlId().'" name="'.$this->getName().'" '.$this->serialize($this->getHtmlAttributes()).'>'."\n";
		$values = array(
			array('label' => Mage::helper('vendors')->__('No'),'value'=>0),
			array('label' => Mage::helper('vendors')->__('Yes'),'value'=>1),
		);

            foreach ($values as $key => $option) {
                if (!is_array($option)) {
                    $html.= $this->_optionToHtml(array(
                        'value' => $key,
                        'label' => $option),
                        $value
                    );
                }
                elseif (is_array($option['value'])) {
                    $html.='<optgroup label="'.$option['label'].'">'."\n";
                    foreach ($option['value'] as $groupItem) {
                        $html.= $this->_optionToHtml($groupItem, $value);
                    }
                    $html.='</optgroup>'."\n";
                }
                else {
                    $html.= $this->_optionToHtml($option, $value);
                }
            }

        $html.= '</select>'."\n";
        $html.= $this->getAfterElementHtml();
        return $html;
    }

    protected function _optionToHtml($option, $selected)
    {
        if (is_array($option['value'])) {
            $html ='<optgroup label="'.$option['label'].'">'."\n";
            foreach ($option['value'] as $groupItem) {
                $html .= $this->_optionToHtml($groupItem, $selected);
            }
            $html .='</optgroup>'."\n";
        }
        else {
            $html = '<option value="'.$this->_escape($option['value']).'"';
            $html.= isset($option['title']) ? 'title="'.$this->_escape($option['title']).'"' : '';
            $html.= isset($option['style']) ? 'style="'.$option['style'].'"' : '';
            if (in_array($option['value'], $selected)) {
                $html.= ' selected="selected"';
            }
            $html.= '>'.$this->_escape($option['label']). '</option>'."\n";
        }
        return $html;
    }

    protected function _prepareOptions()
    {
        $values = $this->getValues();
        if (empty($values)) {
            $options = $this->getOptions();
            if (is_array($options)) {
                $values = array();
                foreach ($options as  $value => $label) {
                    $values[] = array('value' => $value, 'label' => $label);
                }
            } elseif (is_string($options)) {
                $values = array( array('value' => $options, 'label' => $options) );
            }
            $this->setValues($values);
        }
    }

    public function getHtmlAttributes()
    {
        return array('title', 'class', 'style', 'onclick', 'onchange', 'disabled', 'readonly', 'tabindex');
    }
}
