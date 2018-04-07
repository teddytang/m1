<?php

class VES_VendorsConfig_Block_Config_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Get sections from xml files.
	 */
  	protected function _getSections(){
  		return $this->_sortElements(Mage::helper('vendorsconfig')->getConfig()->getSections());
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
  	
  	public function getCurrentSection(){
  		$currentSectionId = $this->getRequest()->getParam('section',false);
  		$sections = $this->_getSections();
  		if($sections && is_array($sections)) foreach($sections as $section){
  			if(!$currentSectionId) return $section;
  			if($section['element_id'] == $currentSectionId) return $section;
  		}
  		return reset($sections);
  	}
  	
	protected function _prepareForm()
  	{
      	$form = new Varien_Data_Form(array(
                                      'id' => 'edit_form',
                                      'action' => $this->getUrl('*/*/save',array('section'=>$this->getRequest()->getParam('section',false))),
                                      'method' => 'post',
        							  'enctype' => 'multipart/form-data'
                                   )
      	);

      	$form->setUseContainer(true);
      	$this->setForm($form);
      	
      	$section = $this->getCurrentSection();
      	
		$sectionName= $section['element_id'];
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
		
		foreach($groups as $group){
			$groupName 	= $group['element_id'];
			$groupObj 	= new Varien_Object($group);
			Mage::dispatchEvent('ves_vendorsconfig_form_fieldset_prepare_before',array(
				'group_id'	=> $sectionName.'_'.$groupName,
				'form'		=> $this->getForm(),
				'group'		=> $groupObj,
			));
			$group	= $groupObj->getData();
			$fields = isset($group['fields']) && is_array($group['fields']) && sizeof($group['fields'])?$group['fields']:false;
			if(!$fields) continue;
			$fieldset = $form->addFieldset('fieldset_'.$sectionName.'_'.$groupName, array('legend'=>$helper->__($group['label'])));
			$fields = $this->_sortElements($fields);
			foreach ($fields as $field){
				$fieldName 	= $field['element_id'];
				$fieldObj 	= new Varien_Object($field);
				Mage::dispatchEvent('ves_vendorsconfig_form_field_prepare_before',array(
					'field_id'	=> $sectionName.'_'.$groupName.'_'.$fieldName,
					'is_allow'	=> true,
					'field'		=> $fieldObj,
					'fieldset'	=> $fieldset,
					'form'		=> $this->getForm(),
				));
				
				$field 		= $fieldObj->getData();
				if(isset($field['renderer'])&&$field['renderer']){
					$fieldset->addType($field['frontend_type'], $field['renderer']);
				}
				
                
                $path 	= $sectionName.'/'.$groupName.'/'.$fieldName;
                $value 	= Mage::helper('vendorsconfig')->getVendorConfig($path,Mage::getSingleton('vendors/session')->getVendor()->getId());
				if (isset($field['backend_model']) && $field['backend_model']) {
                    $model = Mage::getModel((string)$field['backend_model']);
                    if (!$model instanceof Mage_Core_Model_Config_Data) {
                        Mage::throwException('Invalid config field backend model: '.(string)$element->backend_model);
                    }
                    $model->setPath($path)
                        ->setValue($value)
                        ->setWebsite($this->getWebsiteCode())
                        ->setStore($this->getStoreCode())
                        ->afterLoad();
                    $value = $model->getValue();
                }
				$formField = $fieldset->addField($sectionName.'_'.$groupName.'_'.$fieldName, $field['frontend_type'], array(
				  	'label'     => $helper->__($field['label']),
				  	'class'     => isset($field['frontend_class'])&&$field['frontend_class']?$field['frontend_class']:'',
				  	'required'  => isset($field['required'])&&$field['required'],
				  	'name'      => 'config['.$sectionName.']['.$groupName.']['.$fieldName.']',
				  	'values'	=> isset($field['source_model']) && $field['source_model']?Mage::getModel($field['source_model'])->toOptionArray():null,
				  	'note'	  => isset($field['comment'])&&$field['comment']? $helper->__($field['comment']):'',
					'value'		=> $value,
				));
				if (isset($field['frontend_model']) && $field['frontend_model']) {
                    $fieldRenderer = Mage::getBlockSingleton((string)$field['frontend_model']);
                    $fieldRenderer->setForm($this);
					$formField->setRenderer($fieldRenderer);
                } 
                
			}
			Mage::dispatchEvent('ves_vendorsconfig_form_fieldset_prepare_after',array(
				'fieldset_id'	=> 'section_group_'.$groupName,
				'fieldset'		=> $fieldset,
				'form'			=> $this->getForm(),
			));
		}
		
		if ( Mage::getSingleton('adminhtml/session')->getConfigData() )
		{
		  $form->setValues(Mage::getSingleton('adminhtml/session')->getConfigData());
		  Mage::getSingleton('adminhtml/session')->setConfigData(null);
		} else{
			/*$vendorId = Mage::getSingleton('vendors/session')->getVendorId();
			$configCollection = Mage::getModel('vendorsconfig/config')->getCollection()->addFieldToFilter('vendor_id',$vendorId);
			$defaultConfigs = Mage::getConfig()->getNode('vendor_config')->asArray();
			foreach($defaultConfigs as $sectionId=>$section){
				foreach($section as $groupId=>$group){
					foreach($group as $fieldId=>$fieldValue){
						$element = $form->getElement($sectionId.'_'.$groupId.'_'.$fieldId);
						if($element){
							$element->setValue($fieldValue);
						}
					}
				}
			}
			
			foreach($configCollection as $config){
				$elementId = str_replace("/", "_", $config->getPath());
				$element = $form->getElement($elementId);
				if($element){
					$element->setValue($config->getValue());
				}
			}*/
		}
      return parent::_prepareForm();
  	}

}