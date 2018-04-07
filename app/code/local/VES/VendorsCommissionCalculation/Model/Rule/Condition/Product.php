<?php
/**
 * Rule
 *
 * @category   VES
 * @package    VES_VBlock
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsCommissionCalculation_Model_Rule_Condition_Product extends Mage_CatalogRule_Model_Rule_Condition_Product
{
	public function getTypeElement()
    {
        return $this->getForm()->addField($this->getPrefix() . '__{{number}}__' . $this->getId() . '__type', 'hidden', array(
            'name'    => 'rule[{{number}}][' . $this->getPrefix() . '][' . $this->getId() . '][type]',
            'value'   => $this->getType(),
            'no_span' => true,
            'class'   => 'hidden',
        ));
    }
    

	/**
     * Retrieve Condition Operator element Instance
     * If the operator value is empty - define first available operator value as default
     *
     * @return Varien_Data_Form_Element_Select
     */
    public function getOperatorElement()
    {
        $options = $this->getOperatorSelectOptions();
        if (is_null($this->getOperator())) {
            foreach ($options as $option) {
                $this->setOperator($option['value']);
                break;
            }
        }

        $elementId   = sprintf('%s__{{number}}__%s__operator', $this->getPrefix(), $this->getId());
        $elementName = sprintf('rule[{{number}}][%s][%s][operator]', $this->getPrefix(), $this->getId());
        $element     = $this->getForm()->addField($elementId, 'select', array(
            'name'          => $elementName,
            'values'        => $options,
            'value'         => $this->getOperator(),
            'value_name'    => $this->getOperatorName(),
        ));
        $element->setRenderer(Mage::getBlockSingleton('rule/editable'));

        return $element;
    }
    
	public function getValueElement()
    {
        $elementParams = array(
            'name'               => 'rule[{{number}}]['.$this->getPrefix().']['.$this->getId().'][value]',
            'value'              => $this->getValue(),
            'values'             => $this->getValueSelectOptions(),
            'value_name'         => $this->getValueName(),
            'after_element_html' => $this->getValueAfterElementHtml(),
            'explicit_apply'     => $this->getExplicitApply(),
        );
        if ($this->getInputType()=='date') {
            // date format intentionally hard-coded
            $elementParams['input_format'] = Varien_Date::DATE_INTERNAL_FORMAT;
            $elementParams['format']       = Varien_Date::DATE_INTERNAL_FORMAT;
        }
        return $this->getForm()->addField($this->getPrefix().'__{{number}}__'.$this->getId().'__value',
            $this->getValueElementType(),
            $elementParams
        )->setRenderer($this->getValueElementRenderer());
    }
	
    public function getAttributeElement()
    {
        if (is_null($this->getAttribute())) {
            foreach ($this->getAttributeOption() as $k => $v) {
                $this->setAttribute($k);
                break;
            }
        }
        $element = $this->getForm()->addField($this->getPrefix().'__{{number}}__'.$this->getId().'__attribute', 'select', array(
            'name'=>'rule[{{number}}]['.$this->getPrefix().']['.$this->getId().'][attribute]',
            'values'=>$this->getAttributeSelectOptions(),
            'value'=>$this->getAttribute(),
            'value_name'=>$this->getAttributeName(),
        ))->setRenderer(Mage::getBlockSingleton('rule/editable'));
        
        $element->setShowAsText(true);
        return $element;
    }
}