<?php

class VES_VendorsCommissionCalculation_Model_Source_Status extends Varien_Object
{
    const STATUS_ENABLED	= 1;
    const STATUS_DISABLED	= 0;

    static public function getOptionArray()
    {
        return array(
            self::STATUS_ENABLED    => Mage::helper('vendorscommission')->__('Active'),
            self::STATUS_DISABLED   => Mage::helper('vendorscommission')->__('Inactive')
        );
    }

    static public function getOptions(){
        return
        array(
            array(
                'value'     => self::STATUS_ENABLED,
                'label'     => Mage::helper('vendorscommission')->__('Active'),
            ),

            array(
                'value'     => self::STATUS_DISABLED,
                'label'     => Mage::helper('vendorscommission')->__('Inactive'),
            ),
        );
    }
}