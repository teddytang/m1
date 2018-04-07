<?php
class VES_VendorsMessage_Block_Links extends Mage_Core_Block_Template
{
    public function getUnreadMessageCount(){
      	return  $this->helper('vendorsmessage')->getUnreadMessageCount(Mage::getSingleton('vendors/session')->getVendorId(), VES_VendorsMessage_Model_Message::TYPE_VENDOR);
    }
	protected function _prepareLayout(){
		if(!Mage::helper('vendorsmessage')->moduleEnable()) return;
    	$topLinkBlock = $this->getLayout()->getBlock('toplinks');
    	$messageMenuHtml = '
    		<ul class="message-menu">
		        <!--<li><a href="'.$this->helper('vendorsmessage')->getNewMessageUrl().'" title="" class="sAdd">'.$this->__('new message').'</a></li>-->
		        <li><a href="'.$this->helper('vendorsmessage')->getInboxUrl().'" title="" class="sInbox">'.$this->__('inbox').'</a></li>
		        <li><a href="'.$this->helper('vendorsmessage')->getOutboxUrl().'" title="" class="sOutbox">'.$this->__('outbox').'</a></li>
		        <li><a href="'.$this->helper('vendorsmessage')->getTrashUrl().'" title="" class="sTrash">'.$this->__('trash').'</a></li>
	      	</ul>
	      <script type="text/javascript">
	      	var VENDORS_MESSAGE_MENU_STATUS = 0;
	      	Event.observe(window,\'click\',function(){if(VENDORS_MESSAGE_MENU_STATUS == 0) $$(\'#user-nav .message\').first().removeClassName(\'open\');});
	      	$$(\'#user-nav .message\').first().observe(\'mousemove\',function(){VENDORS_MESSAGE_MENU_STATUS = 1;});
	      	$$(\'#user-nav .message\').first().observe(\'mouseout\',function(){VENDORS_MESSAGE_MENU_STATUS = 0;});
	      </script>
    	';
    	$messageCount = $this->getUnreadMessageCount();
    	$topLinkBlock->addLink($this->__('Messages'), 'javascript: void(0);',$this->__('Messages'),false,array(),15,'class="message"','onclick="this.parentNode.toggleClassName(\'open\')"','',$messageMenuHtml,'',($messageCount?'<span class="message-count">'.$messageCount.'</span>':'').'<b class="message-arrow"></b>');
    }
}
