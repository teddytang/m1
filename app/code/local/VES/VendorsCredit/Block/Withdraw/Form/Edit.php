<?php

class VES_VendorsCredit_Block_Withdraw_Form_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'vendorscredit';
        $this->_controller = 'withdraw_form';
        $this->_removeButton('delete');
        $this->_removeButton('reset');
        $this->_updateButton('save', 'label', Mage::helper('vendorscredit')->__('Continue'));
        /*$this->_formScripts[] = "
            Validation.add('validate-less-than', 'Withdraw amount must be less than your balance.', function (v,elm) {
			     if (Validation.get('IsEmpty').test(v)) {
                    return true;
                 }
                 var numValue = parseNumber(v);
                 if (isNaN(numValue)) {
                    return false;
                 }

                 var reRange = /^less-than-(-?[\d.,]+)?$/,
                    result = true;
                 $w(elm.className).each(function(name) {
                    var m = reRange.exec(name);
                    if (m) {
                        result = result
                            && (m[1] == null || m[1] == '' || numValue <= parseNumber(m[1]));
                    }
                });
                return result;
			});
        ";*/
    }

    public function getHeaderText()
    {
    	return Mage::helper('vendorscredit')->__('Withdraw Funds');
    }
    public function getBackUrl(){
    	return $this->getUrl('vendors/credit_withdraw');
    }
}