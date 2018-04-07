<?php

class VES_VendorsGroup_Model_Observer
{	
	/**
	 * Add advanced option tab for group
	 * @param Varien_Event_Observer $observer
	 */
	public function ves_vendors_group_prepare_tabs_after(Varien_Event_Observer $observer){
		$tabsBlock = $observer->getEvent()->getTabs();
		$tabsBlock->addTab('advanced_option_section', array(
			'label'     => Mage::helper('vendorsgroup')->__('Advanced Options'),
			'title'     => Mage::helper('vendorsgroup')->__('Advanced Options'),
			'content'   => $tabsBlock->getLayout()->createBlock('vendorsgroup/adminhtml_vendors_group_edit_tab_option')->toHtml(),
		));
	}
	
	/**
	 * Save advanced options of groups
	 * @param Varien_Event_Observer $observer
	 */
	public function ves_vendors_admin_group_save_after(Varien_Event_Observer $observer){
		$group	= $observer->getEvent()->getGroup(); /*VES_Vendors_Model_Group*/
		$action = $observer->getEvent()->getAction(); /*Mage_Adminhtml_Controller_Action */
		$config = $action->getRequest()->getParam('config');
		foreach($config as $sectionName=>$data){
			foreach($data as $fieldName=>$value){
				$resourceId = $sectionName."/".$fieldName;
				/*Load Resource By Resources Id*/
				$collection = Mage::getModel('vendorsgroup/rule')->getCollection()
							->addFieldToFilter('resource_id',$resourceId)
							->addFieldToFilter('group_id',$group->getId());
				if($collection->count()){
					$resource	= $collection->getFirstItem();
				}else{
					$resource	= Mage::getModel('vendorsgroup/rule');
				}
				$resource->setGroupId($group->getId());
				$resource->setResourceId($resourceId);
				$resource->setValue($value);
				$resource->save();
			}
		}
	}
	
	/**
	 * Delete all rule of group if that group is deleted.
	 * @param Varien_Event_Observer $observer
	 */
	public function vendor_group_delete_after(Varien_Event_Observer $observer){
		$group	= $observer->getEvent()->getVendorGroup();
		Mage::getResourceModel('vendorsgroup/rule')->deleteRulesByGroup($group);
	}
}