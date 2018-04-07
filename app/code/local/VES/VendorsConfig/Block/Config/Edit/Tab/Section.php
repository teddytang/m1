<?php
class VES_VendorsConfig_Block_Config_Edit_Tab_Section extends Mage_Adminhtml_Block_Widget_Form
{
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
	protected function _prepareForm()
	{
		$section 	= $this->getSection();
		$sectionName= $this->getSectionName();
		
		$helper = Mage::helper('vendorsconfig');
		if(isset($section['@'])){
			if(isset($section['@']['module'])){
				$helper = Mage::helper($section['@']['module']);
			}
		}
		if(!$section) return parent::_prepareForm();
		$groups = isset($section['groups']) && is_array($section['groups'])?$section['groups']:false;
		if(!$groups) return parent::_prepareForm();
		/*Sort groups*/
		$groups = $this->_sortElements($groups);
		
		$form = new Varien_Data_Form();
		$this->setForm($form);
		foreach($groups as $groupName=>$group){
			$fieldset = $form->addFieldset('section_group_'.$groupName, array('legend'=>$helper->__($group['label'])));
			$fields = isset($group['fields']) && is_array($group['fields'])?$group['fields']:false;
			if(!$fields) continue;
			$fields = $this->_sortElements($fields);
			foreach ($fields as $fieldName=>$field){
				$fieldset->addField($sectionName.'_'.$groupName.'_'.$fieldName, $field['frontend_type'], array(
				  'label'     => $helper->__($field['label']),
				  'class'     => isset($field['frontend_class'])&&$field['frontend_class']?$field['frontend_class']:'',
				  'required'  => isset($field['required'])&&$field['required'],
				  'name'      => 'config['.$sectionName.']['.$groupName.']['.$fieldName.']',
				  'values'	  => isset($field['source_model']) && $field['source_model']?Mage::getModel($field['source_model'])->toOptionArray():null,
				  'note'	  => isset($field['comment'])&&$field['comment']? $helper->__($field['comment']):'',
				));
			}
		}
		if ( Mage::getSingleton('vendors/session')->getVendorsData() )
		{
		  $form->setValues(Mage::getSingleton('vendors/session')->getVendorsData());
		  Mage::getSingleton('vendors/session')->setVendorsData(null);
		} elseif ( Mage::registry('vendors_data') ) {
		  $form->setValues(Mage::registry('vendors_data')->getData());
		}
		return parent::_prepareForm();
	}
}