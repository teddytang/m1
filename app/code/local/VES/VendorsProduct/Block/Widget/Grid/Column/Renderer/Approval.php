<?php

/**
 * Adminhtml grid item renderer
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */

class VES_VendorsProduct_Block_Widget_Grid_Column_Renderer_Approval
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
        $value = $row->getData($this->getColumn()->getIndex());
        $className = '';
        switch ($value){
        	case VES_VendorsProduct_Model_Source_Approval::STATUS_NOT_SUBMITED:
        		$className = 'status_black';
        		break;
        	case VES_VendorsProduct_Model_Source_Approval::STATUS_PENDING:
        		$className = 'status_yellow';
        		break;
        	case VES_VendorsProduct_Model_Source_Approval::STATUS_APPROVED:
        		$className = 'status_green';
        		break;
        	case VES_VendorsProduct_Model_Source_Approval::STATUS_UNAPPROVED:
        		$className = 'status_red';
        		break;
        }
        $text = '<span class="'.$className.'">'.$text.'</span>';
        return $text;
    }
}
