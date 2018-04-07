<?php
class VES_VendorsMessage_Block_Form_Element_Editor extends Varien_Data_Form_Element_Editor
{
	public function getAfterElementHtml()
    {
        $afterHtml = $this->getData('after_element_html');
        $afterHtml .='<button style="margin-top: 10px;" onclick="editForm.submit();" class="right scalable save" type="button" title="'.Mage::helper('vendorsmessage')->__('Send Message').'" id="'.$this->getId().'_send_msg_btn"><span><span><span>'.Mage::helper('vendorsmessage')->__('Send Message').'</span></span></span></button>';
        return $afterHtml;
    }
    
	public function getHtml()
    {
    	if ($this->getRequired()) {
            $this->addClass('required-entry');
        }
        $html = '<tr>
			    <td class="value" colspan="2"><div style="margin-top: 20px;">'.$this->getElementHtml().'</div></td>
			    </tr>';
        return $html;
    }
}