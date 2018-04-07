<?php

/**
 * Catalog manage products block
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsProduct_Block_Adminhtml_Catalog_Product_Pending_Grid extends Mage_Adminhtml_Block_Catalog_Product_Grid
{
	protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('product');
		$this->getMassactionBlock()->addItem('approve', array(
             'label'=> Mage::helper('vendorsproduct')->__('Approve'),
             'url'  => $this->getUrl('*/*/massApprove'),
        ));
        $this->getMassactionBlock()->addItem('reject', array(
             'label'=> Mage::helper('vendorsproduct')->__('Reject'),
             'url'  => $this->getUrl('*/*/massReject'),
        ));
//		$this->getMassactionBlock()->addItem('delete', array(
//             'label'=> Mage::helper('vendorsproduct')->__('Delete'),
//             'url'  => $this->getUrl('*/*/massDelete'),
//             'confirm' => Mage::helper('catalog')->__('Are you sure?')
//        ));
        return $this;
    }
    
    protected function _prepareCollection()
    {
    	$store = $this->_getStore();
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id')
            ->addAttributeToFilter('approval',VES_VendorsProduct_Model_Source_Approval::STATUS_PENDING);
		$collection->joinTable(array('vendor_table'=>'vendors/vendor'),'entity_id = vendor_id',array('vendor'=>'vendor_id'));
        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $collection->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');
        }
        if ($store->getId()) {
            //$collection->setStoreId($store->getId());
            $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
            $collection->addStoreFilter($store);
            $collection->joinAttribute(
                'name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                $adminStore
            );
            $collection->joinAttribute(
                'custom_name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'status',
                'catalog_product/status',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'visibility',
                'catalog_product/visibility',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'price',
                'catalog_product/price',
                'entity_id',
                null,
                'left',
                $store->getId()
            );
        }
        else {
            $collection->addAttributeToSelect('price');
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
        }

        $this->setCollection($collection);

        Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
        $this->getCollection()->addWebsiteNamesToResult();
    	return $this;
    }
    
    
    protected function _prepareColumns(){
		parent::_prepareColumns();
		$this->_rssLists = array();
		
		$this->addColumnAfter('vendor',
            array(
                'header'=> Mage::helper('vendorsproduct')->__('Vendor'),
                'width' => '80px',
                'index' => 'vendor',
            	'renderer'	=> new VES_VendorsProduct_Block_Widget_Grid_Column_Renderer_Text(),
        ),'entity_id');
        $acionData = array(
                    array(
                        'caption' => Mage::helper('catalog')->__('Edit'),
                        'url'     => array(
                            'base'=>'adminhtml/catalog_product/edit',
                            'params'=>array('store'=>$this->getRequest()->getParam('store'))
                        ),
                        'field'   => 'id'
                    )
                );
        $this->getColumn('action')->setData('actions',$acionData);
		return Mage_Adminhtml_Block_Widget_Grid::_prepareColumns();
    }
    
	public function getRowUrl($row)
    {
        return '';
    }
}
