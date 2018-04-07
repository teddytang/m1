<?php

/**
 * Adminhtml grid item renderer
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */

class VES_VendorsCredit_Block_Widget_Grid_Column_Renderer_Options
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Options
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
        switch($row->getStatus()){
        	case VES_VendorsCredit_Model_Withdrawal::STATUS_PENDING:
        		$text = '<span class="status_yellow">'.$text.'</span>';
        		break;
        	case VES_VendorsCredit_Model_Withdrawal::STATUS_COMPLETE:
        		$text = '<span class="status_green">'.$text.'</span>';
        		break;
        	case VES_VendorsCredit_Model_Withdrawal::STATUS_CANCELED:
        		$text = '<span class="status_gray">'.$text.'</span>';
        		break;
        	
        }
        return $text;
    }
}
