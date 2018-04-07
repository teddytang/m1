<?php
class VES_VendorsMessage_Block_Message extends Mage_Adminhtml_Block_Widget_Grid_Container{
	public function __construct(){
		$this->_controller = 'message';
		$this->_blockGroup = 'vendorsmessage';
		$this->_headerText = $this->getTitle();
		$this->_addButtonLabel = Mage::helper('vendors')->__('New Message');
		parent::__construct();
		if($this->getRequest()->getActionName() != 'inbox'){
			$this->removeButton('add');
		}
	}
	
	/**
	 * Get Title
	 */
	public function getTitle(){
		$action = $this->getRequest()->getActionName();
		switch($action){
			case 'inbox':
				return Mage::helper('vendors')->__('Inbox');
			case 'outbox':
				return Mage::helper('vendors')->__('Outbox');
			case 'trash':
				return Mage::helper('vendors')->__('Trash');
		}
	}
}