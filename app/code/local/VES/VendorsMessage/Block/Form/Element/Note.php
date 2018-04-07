<?php
class VES_VendorsMessage_Block_Form_Element_Note extends Varien_Data_Form_Element_Abstract
{
	public function __construct($attributes=array())
	{
		parent::__construct($attributes);
		$this->setType('note');
	}
	
	public function getMessageContent(VES_VendorsMessage_Model_Message $message){
		$backgroundColor = $message->getData('from_msgbox_id')==Mage::registry('message_box')->getId()?'fffeed':'CDE0F7';
		$html = '';
		$html .='<div id="ves-message-'.$message->getId().'" style="border: 1px solid #d3d3d3; padding: 20px;margin: 10px 0;border-radius: 5px; -moz-border-radius: 5px; -webkit-border-radius: 5px;background: #'.$backgroundColor.';">';
		$html .='<div style="float: left;font-size: 14px;"><span style="font-weight: bold;">'.($message->getFromMsgbox()->getType()==VES_VendorsMessage_Model_Message::TYPE_ADMIN?'[ADMIN] ':'').$message->getFromMsgbox()->getName().'</span> &lt;'.$message->getFrom().'&gt;</div>';
		$html .='<div style="float: right;font-style: italic;">'.Mage::getModel('core/date')->date('M d, Y h:s:i A',$message->getCreatedAt()).'</div>';
		$html .='<div style="clear: both;border-top: 1px dashed #D3D3D3;margin-bottom: 10px;"></div>';
		$html .= str_replace("\n","<br />",$message->getContent());
		$html .= '</div>';
		
		return $html;
	}
	/**
	 * Retrieve Element HTML
	 *
	 * @return string
	 */
	public function getHtml()
	{
		$message = $this->getMessage();
		$html = '<tr><td colspan="2">';
		if($message->getParentMessageId()){
			$html.= $this->getMessageContent($message->getParentMessage());
			$msgbox = Mage::registry('message_box');
			$collection = Mage::getModel('vendorsmessage/message')->getCollection()
			->addFieldToFilter('parent_message_id',$message->getParentMessageId())
			->addFieldToFilter('msgbox_id',$msgbox->getId())
			->addOrder('created_at','ASC');
			foreach($collection as $msg){
				$html.= $this->getMessageContent($msg);
			}
		}
		else{
			$html .= $this->getMessageContent($message);
		}
		/*$html .='<div style="border: 1px solid #d3d3d3; padding: 20px;margin: 10px 0;border-radius: 5px; -moz-border-radius: 5px; -webkit-border-radius: 5px;background: #fffeed;">';
		$html .='<h3 style="float: left;">'.$message->getFrom().'</h3>';
		$html .='<div style="float: right;font-style: italic;">'.Mage::getModel('core/date')->date('M d, Y h:s:i A',$message->getCreatedAt()).'</div>';
		$html .='<div style="clear: both;border-top: 1px dashed #D3D3D3;margin-bottom: 10px;"></div>';
		$html .= str_replace("\n","<br />",$this->getValue());
		$html .= '</div>';*/
		
		$html .='</td><tr>';
		return $html;
	}
}