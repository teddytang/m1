<?php

class VES_VendorsGroup_Block_Adminhtml_Vendors_Group_Edit_Tab_Option extends Mage_Adminhtml_Block_Widget_Form
{
	/**
  	 * Get all sections
  	 */
  	public function getSections(){
  		return Mage::helper('vendorsgroup')->getSections();
  	}
	
	/**
  	 * Sort element by sort_order
  	 */
  	protected function _sortElements($sections){
	  	$sortedSections = array();
	  	$previousOrder = 0;
	  	foreach($sections as $key=>$section){
	  		$index = isset($section['sort_order']) && $section['sort_order']?$section['sort_order']:++$previousOrder;
	  		$section['element_id'] = $key;
	  		if(!isset($sortedSections[$index])){
	  			$sortedSections[$index] = $section;
	  		}else{
	  			while(isset($sortedSections[$index])){
	  				$index++;
	  			}
	  			$sortedSections[$index] = $section;
	  		}
	  		$previousOrder = $index;
	  	}
	  	ksort($sortedSections);
	  	return $sortedSections;
  	}
  	
	protected function _prepareForm(){
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$sections = $this->_sortElements($this->getSections());
		foreach($sections as $section){
			$key = $section['element_id'];
			/*Get helper by module attribute*/
			$helper = Mage::helper('vendorsgroup');
			if(isset($section['@'])){
				if(isset($section['@']['module'])){
					$helper = Mage::helper($section['@']['module']);
				}
			}
			
			if(isset($section['fields']) && is_array($section['fields']) && sizeof($section['fields'])){
				$fieldset = $form->addFieldset('advanced_group_'.$key, array('legend'=>$helper->__($section['title'])));
				$fields = $this->_sortElements($section['fields']);
				foreach($fields as $field){
					$fieldId = $field['element_id'];
					$fieldHelper = $helper;
					if(isset($field['@']) && isset($field['@']['module'])){
						$fieldHelper = Mage::helper($field['@']['module']);
					}
					/*Add field*/
					$fieldset->addField('vendorsgroup_'.$key.'_'.$fieldId, $field['frontend_type'], array(
					  	'label'		=> $fieldHelper->__($field['label']),
					  	'class'     => isset($field['frontend_class'])&&$field['frontend_class']?$field['frontend_class']:'',
					  	'required'  => isset($field['required'])&&$field['required'],
					  	'name'      => 'config['.$key.']['.$fieldId.']',
					  	'values'	=> isset($field['source_model']) && $field['source_model']?Mage::getModel($field['source_model'])->toOptionArray():null,
					  	'note'	  	=> isset($field['comment'])&&$field['comment']?$field['comment']:'',
					));
				}
			}
		}
			
		
		/*Fill data for fields*/
		if ($group = Mage::registry('group_data') ) {
			$options = Mage::getModel('vendorsgroup/rule')->getCollection()->addFieldToFilter('group_id',$group->getId());
			foreach($options as $option){
				$resource 	= explode("/",$option->getResourceId());
				if(sizeof($resource) != 2) continue;
				$elementId 	= 'vendorsgroup_'.$resource[0].'_'.$resource[1];
				if($element	= $form->getElement($elementId)){
					$element->setValue($option->getValue());
				}
			}
		}
		return parent::_prepareForm();
	}
}