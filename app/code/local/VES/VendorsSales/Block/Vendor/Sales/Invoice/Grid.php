<?php

/**
 * Adminhtml sales orders grid
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class VES_VendorsSales_Block_Vendor_Sales_Invoice_Grid extends Mage_Adminhtml_Block_Sales_Invoice_Grid
{

    public function __construct()
    {
        parent::__construct();
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $vendorId	= Mage::getSingleton('vendors/session')->getVendorId();
        $collection->addFieldToFilter('vendor_id',$vendorId);
        $this->setCollection($collection);
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    protected function _prepareColumns()
    {
    	parent::_prepareColumns();
        return Mage_Adminhtml_Block_Widget_Grid::_prepareColumns(); 
    }

    protected function _prepareMassaction()
    {
        return parent::_prepareMassaction();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/sales_invoice/view',
            array(
                'invoice_id'=> $row->getId(),
            )
        );
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

}
