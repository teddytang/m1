<?php

class VES_VendorsMessage_Block_Message_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'message_id';
        $this->_blockGroup = 'vendorsmessage';
        $this->_controller = 'message';
        parent::__construct();
        $this->_removeButton('save');
        $this->_removeButton('reset');
        $objectId = $this->getRequest()->getParam($this->_objectId);
        if($objectId){
			$this->_addButton('mark_unread', array(
	            'label'     => Mage::helper('vendorsmessage')->__('Mark As Unread'),
	            'onclick'   => 'setLocation(\'' . $this->getUnreadUrl() . '\')',
	            'class'     => 'save',
	        ), -1);
        }
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('vendors_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'vendors_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'vendors_content');
                }
            }
            function selectRecipientType(){
            	if($('recipient_type').value =='customer' || $('recipient_type').value =='vendor') { 
            		$('to').up(1).show();
    				$('to').addClassName('required-entry');
    			}else {
    				$('to').up(1).hide();
            		$('to').removeClassName('required-entry');
   				}
            }
            $('recipient_type').observe('change', function(){
            	selectRecipientType();
            });
            selectRecipientType();
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('message_data') && Mage::registry('message_data')->getId() ) {
            return Mage::helper('vendorsmessage')->__("%s", $this->htmlEscape(Mage::registry('message_data')->getSubject()));
        } else {
            return Mage::helper('vendorsmessage')->__('New Message');
        }
    }
	/**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/inbox');
    }
    
    /**
     * Get URL for mark as unread button
     *
     * @return string
     */
    public function getUnreadUrl(){
    	return $this->getUrl('*/*/markunread',array('message_id'=>$this->getRequest()->getParam($this->_objectId)));
    }
}