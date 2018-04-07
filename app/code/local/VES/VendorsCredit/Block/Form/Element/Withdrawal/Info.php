<?php
class VES_VendorsCredit_Block_Form_Element_Withdrawal_Info extends Varien_Data_Form_Element_Abstract
{
	public function __construct($attributes=array())
	{
		parent::__construct($attributes);
		$this->setType('note');
	}
	/**
	 * Retrieve Element HTML
	 *
	 * @return string
	 */
	public function getHtml()
	{
		$value = unserialize(base64_decode($this->getValue()));
		$html = '<tr><td colspan="2"><div style="border: 1px solid #d3d3d3; padding: 10px;margin: 10px 0;border-radius: 5px; -moz-border-radius: 5px; -webkit-border-radius: 5px;background: #fffeed;">';
		$html .= '<h3>'.$this->getLabel().'</h3>';
		$html .= $value['account']?$value['account'].'<br />':'';
		$html .= str_replace("\n","<br />",$value['additional_info']);
		$html .= '</div></td><tr>';
		return $html;
	}
}