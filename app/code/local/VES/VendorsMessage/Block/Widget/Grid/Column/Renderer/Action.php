<?php

/**
 * Grid column widget for rendering action grid cells
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsMessage_Block_Widget_Grid_Column_Renderer_Action
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
    /**
     * Render single action as link html
     *
     * @param array $action
     * @param Varien_Object $row
     * @return string
     */
    protected function _toLinkHtml($action, Varien_Object $row)
    {
        $actionAttributes = new Varien_Object();

        $actionCaption = '';
        $this->_transformActionData($action, $actionCaption, $row);
        if(isset($action['confirm'])) {
            $action['onclick'] = 'return window.confirm(\''
                               . addslashes($this->escapeHtml($action['confirm']))
                               . '\')';
            unset($action['confirm']);
        }

        $actionAttributes->setData($action);
        if($row->getState() == VES_VendorsMessage_Model_Message::STATE_UNREAD)
        	return '<a ' . $actionAttributes->serialize() . '><span style="font-weight: bold;">' . $actionCaption . '</span></a>';
        return '<a ' . $actionAttributes->serialize() . '>' . $actionCaption . '</a>';
    }
}
