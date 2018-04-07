<?php

/**
 * Adminhtml grid item renderer
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */

class VES_VendorsCredit_Block_Widget_Grid_Column_Renderer_Credit
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Currency
{
	/**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $text = parent::render($row);
        $text = '<span style="color: #996515;font-weight: bold;">'.$text.'</span>';
        return $text;
    }
}
