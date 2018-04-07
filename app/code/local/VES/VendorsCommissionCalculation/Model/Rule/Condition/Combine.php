<?php
/**
 * Rule
 *
 * @category   VES
 * @package    VES_VendorsCommissionCalculation
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsCommissionCalculation_Model_Rule_Condition_Combine extends Mage_CatalogRule_Model_Rule_Condition_Combine
{
	public function __construct()
    {
        parent::__construct();
        $this->setType('vendorscommission/rule_condition_combine');
    }
    
	public function getNewChildSelectOptions()
    {
        $conditions = parent::getNewChildSelectOptions();
        
        /* $productCondition = Mage::getModel('vendorscommission/rule_condition_product');
        $productAttributes = $productCondition->loadAttributeOptions()->getAttributeOption();
        $attributes = array();
        foreach ($productAttributes as $code=>$label) {
            $attributes[] = array('value'=>'vendorscommission/rule_condition_product|'.$code, 'label'=>$label);
        }
        
        $conditions = array_merge_recursive($conditions, array(
            array('value'=>'vendorscommission/rule_condition_combine', 'label'=>Mage::helper('catalogrule')->__('Conditions Combination')),
            array('label'=>Mage::helper('catalogrule')->__('Product Attribute'), 'value'=>$attributes),
        )); */
        return $conditions;
    }
    
// 	public function getTypeElement()
//     {
//         return $this->getForm()->addField($this->getPrefix() . '__{{number}}__' . $this->getId() . '__type', 'hidden', array(
//             'name'    => 'rule[{{number}}][' . $this->getPrefix() . '][' . $this->getId() . '][type]',
//             'value'   => $this->getType(),
//             'no_span' => true,
//             'class'   => 'hidden',
//         ));
//     }
    
// 	public function getAttributeElement()
//     {
//         if (is_null($this->getAttribute())) {
//             foreach ($this->getAttributeOption() as $k => $v) {
//                 $this->setAttribute($k);
//                 break;
//             }
//         }
//         return $this->getForm()->addField($this->getPrefix().'__{{number}}__'.$this->getId().'__attribute', 'select', array(
//             'name'=>'rule[{{number}}]['.$this->getPrefix().']['.$this->getId().'][attribute]',
//             'values'=>$this->getAttributeSelectOptions(),
//             'value'=>$this->getAttribute(),
//             'value_name'=>$this->getAttributeName(),
//         ))->setRenderer(Mage::getBlockSingleton('rule/editable'));
//     }
    
// 	/**
//      * Retrieve Condition Operator element Instance
//      * If the operator value is empty - define first available operator value as default
//      *
//      * @return Varien_Data_Form_Element_Select
//      */
//     public function getOperatorElement()
//     {
//         $options = $this->getOperatorSelectOptions();
//         if (is_null($this->getOperator())) {
//             foreach ($options as $option) {
//                 $this->setOperator($option['value']);
//                 break;
//             }
//         }

//         $elementId   = sprintf('%s__{{number}}__%s__operator', $this->getPrefix(), $this->getId());
//         $elementName = sprintf('rule[{{number}}][%s][%s][operator]', $this->getPrefix(), $this->getId());
//         $element     = $this->getForm()->addField($elementId, 'select', array(
//             'name'          => $elementName,
//             'values'        => $options,
//             'value'         => $this->getOperator(),
//             'value_name'    => $this->getOperatorName(),
//         ));
//         $element->setRenderer(Mage::getBlockSingleton('rule/editable'));

//         return $element;
//     }
    
// 	public function getValueElement()
//     {
//         $elementParams = array(
//             'name'               => 'rule[{{number}}]['.$this->getPrefix().']['.$this->getId().'][value]',
//             'value'              => $this->getValue(),
//             'values'             => $this->getValueSelectOptions(),
//             'value_name'         => $this->getValueName(),
//             'after_element_html' => $this->getValueAfterElementHtml(),
//             'explicit_apply'     => $this->getExplicitApply(),
//         );
//         if ($this->getInputType()=='date') {
//             // date format intentionally hard-coded
//             $elementParams['input_format'] = Varien_Date::DATE_INTERNAL_FORMAT;
//             $elementParams['format']       = Varien_Date::DATE_INTERNAL_FORMAT;
//         }
//         return $this->getForm()->addField($this->getPrefix().'__{{number}}__'.$this->getId().'__value',
//             $this->getValueElementType(),
//             $elementParams
//         )->setRenderer($this->getValueElementRenderer());
//     }
    
// 	public function getAggregatorElement()
//     {
//         if (is_null($this->getAggregator())) {
//             foreach ($this->getAggregatorOption() as $k=>$v) {
//                 $this->setAggregator($k);
//                 break;
//             }
//         }
//         return $this->getForm()->addField($this->getPrefix().'__{{number}}__'.$this->getId().'__aggregator', 'select', array(
//             'name'=>'rule[{{number}}]['.$this->getPrefix().']['.$this->getId().'][aggregator]',
//             'values'=>$this->getAggregatorSelectOptions(),
//             'value'=>$this->getAggregator(),
//             'value_name'=>$this->getAggregatorName(),
//         ))->setRenderer(Mage::getBlockSingleton('rule/editable'));
//     }
    
// 	public function getNewChildElement()
//     {
//         return $this->getForm()->addField($this->getPrefix().'__{{number}}__'.$this->getId().'__new_child', 'select', array(
//             'name'=>'rule[{{number}}]['.$this->getPrefix().']['.$this->getId().'][new_child]',
//             'values'=>$this->getNewChildSelectOptions(),
//             'value_name'=>$this->getNewChildName(),
//         ))->setRenderer(Mage::getBlockSingleton('rule/newchild'));
//     }
// 	public function asHtmlRecursive()
//     {
//         $html = $this->asHtml().'<ul id="'.$this->getPrefix().'__{{number}}__'.$this->getId().'__children" class="rule-param-children">';
//         foreach ($this->getConditions() as $cond) {
//             $html .= '<li>'.$cond->asHtmlRecursive().'</li>';
//         }
//         $html .= '<li>'.$this->getNewChildElement()->getHtml().'</li></ul>';
//         return $html;
//     }
}