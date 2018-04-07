<?php

class VES_Vendors_Model_Source_Calculation
{
	const GRANDTOTAL				= 'grandtotal';
	const SUBTOTAL					= 'subtotal';
	const ITEM_ROW_TOTAL			= 'item_row_total';
	const SUBTOTAL_AFTER_DISCOUNT 	= 'subtotal_after_discount';
	static public function toOptionArray()
    {
        return array(
            self::GRANDTOTAL    			=> Mage::helper('vendors')->__('Grand Total'),
            self::SUBTOTAL    				=> Mage::helper('vendors')->__('Subtotal'),
            self::SUBTOTAL_AFTER_DISCOUNT   => Mage::helper('vendors')->__('Subtotal After Discount'),
            self::ITEM_ROW_TOTAL    		=> Mage::helper('vendors')->__('Item Row Total'),
        );
    }
}