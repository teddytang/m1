<?php
class VES_VendorsCredit_Block_Form_Element_Payment extends Varien_Data_Form_Element_Abstract
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
		if($this->getHeader()){
			$html = '<tr style="font-weight: bold;">';
			$html .='<td class="label" style="background: #EEEEEE !important;">'.$this->getPaymentMethod().'</td>';
			$html .='<td class="value" style="width: 400px;background: #EEEEEE !important;">'.$this->getPaymentDescription().'</td>';
			$html .='<td class="label" style="background: #EEEEEE !important;">'.($this->getFeeType()=='currency'?Mage::helper('core')->currency($this->getPaymentFee()):$this->getPaymentFee()).'</td>';
			$html .='</tr>';
		}else{
			$html = '<tr>';
			$html .='<td class="label"><a style="font-weight: bold;" href="'.(Mage::getUrl('vendors/credit_withdraw/form',array('method'=>$this->getMethodId()))).'">'.$this->getPaymentMethod().'</a></td>';
			$html .='<td class="value" style="width: 400px;">'.$this->getPaymentDescription().'</td>';
			$html .='<td class="label">'.($this->getFeeType()=='currency'?Mage::helper('core')->currency($this->getPaymentFee()):$this->getPaymentFee()).'</td>';
			$html .='</tr>';
		}
		return $html;
	}
}