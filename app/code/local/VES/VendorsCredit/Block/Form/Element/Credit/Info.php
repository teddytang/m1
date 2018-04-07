<?php

class VES_VendorsCredit_Block_Form_Element_Credit_Info extends Varien_Data_Form_Element_Abstract

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

		$html = '<tr><td colspan="2"><div style="text-align: center;">';

		$html .= '<p><strong>'.Mage::helper("vendorscredit")->__('Your Credit Balance').'</strong></p>';

		$html .= '<span style="font-size: 40px;font-weight: bold;color: #996515;line-height: 45px;">'.Mage::helper('core')->currency($this->getValue()).'</span>';

		$html .= '</div></td><tr>';

		return $html;

	}

}