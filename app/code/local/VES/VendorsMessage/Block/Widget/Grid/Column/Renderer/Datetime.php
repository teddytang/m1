<?php
/**
 * Adminhtml grid item renderer datetime
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */

class VES_VendorsMessage_Block_Widget_Grid_Column_Renderer_Datetime
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Datetime
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
        if($row->getState() == VES_VendorsMessage_Model_Message::STATE_UNREAD) $text = '<span style="font-weight: bold;">'.$text.'</span>';
        return $text;
    }
}
