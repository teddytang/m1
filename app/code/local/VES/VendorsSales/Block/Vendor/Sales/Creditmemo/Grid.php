<?php

/**
 * Adminhtml sales orders grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class VES_VendorsSales_Block_Vendor_Sales_Creditmemo_Grid extends Mage_Adminhtml_Block_Sales_Creditmemo_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }


    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $vendorId = Mage::getSingleton('vendors/session')->getVendorId();
        $collection->addFieldToFilter('vendor_id',$vendorId);
        $this->setCollection($collection);
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }


    protected function _prepareColumns()
    {
        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        return parent::_prepareMassaction();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/sales_creditmemo/view',
            array(
                'creditmemo_id'=> $row->getId(),
            )
        );
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }



}
