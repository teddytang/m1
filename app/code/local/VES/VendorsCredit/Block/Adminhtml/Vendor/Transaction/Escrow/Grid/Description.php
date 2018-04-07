<?php

/**
 * Escrow description
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */

class VES_VendorsCredit_Block_Adminhtml_Vendor_Transaction_Escrow_Grid_Description
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{
    protected function getTableItems($items){
        $html = '';
        $html .= '<table class="data">';
        $html .= '<thead><tr style="background: #6f8992; color: #FFF;"><th>Product</th><th>Qty</th></tr></thead>';
        $html .= '<tbody>';
        foreach($items as $item){
            $html .= '<tr><td><strong>'.$item->getName().'</strong><br /><strong>SKU: </strong>'.$item->getSku().'</td><td>'.$item->getQty().'</td></tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>';
        return $html;
    }
	/**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $row->getAdditionalInfo();
        if(Mage::helper('vendors')->isAdvancedMode()){
            $invoice    = Mage::getModel('sales/order_invoice')->load($row->getRelationId());
            $items      =  $invoice->getAllItems();
        }else{
            $items      = array(Mage::getModel('sales/order_invoice_item')->load($row->getRelationId()));
        }
        $text = $this->getTableItems($items);
        return $text;
    }
}
