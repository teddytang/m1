<?php
/**
 * Customer edit block
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsProduct_Block_Vendor_Product_Edit_Tabs extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs
{    
	protected $_attributeTabBlock = 'vendorsproduct/vendor_product_edit_tab_attributes';
	protected function _prepareDefaultTabs(){
	    /*Remove some tabs*/
	    $product = $this->getProduct();
	    if (!($setId = $product->getAttributeSetId())) {
	        $setId = $this->getRequest()->getParam('set', null);
	    }
	    $groupCollection = Mage::getResourceModel('eav/entity_attribute_group_collection')
	    ->setAttributeSetFilter($setId)
	    ->setSortOrder()
	    ->load();
	    foreach ($groupCollection as $group) {
	        /*Remove tab 'Recurring Profile','Design','Gift Options'*/
	        if(in_array($group->getData('attribute_group_name'), array('Recurring Profile','Design','Gift Options'))){
	            $this->removeTab('group_'.$group->getId());
	        }
	         
	        if(true/*in_array($group->getData('attribute_group_name'),array('General','Prices'))*/){
	            $attributes = $product->getAttributes($group->getId(), true);
	            // do not add groups without attributes
	    
	            foreach ($attributes as $key => $attribute) {
	                if( !$attribute->getIsVisible() ) {
	                    unset($attributes[$key]);
	                }
	            }
	    
	            if (count($attributes)==0) {
	                continue;
	            }
	            $this->setTabData('group_'.$group->getId(),'content',$this->_translateHtml($this->getLayout()->createBlock('vendorsproduct/vendor_product_edit_tab_attributes')->setGroup($group)->setGroupAttributes($attributes)->toHtml()));
	        }
	         
	    }
	    //$this->removeTab('related');
	    if (!($setId = $product->getAttributeSetId())) {
	        $setId = $this->getRequest()->getParam('set', null);
	    }
	    
	    if ($setId) {
	        $this->removeTab('websites');
	        $this->removeTab('upsell');
	        $this->removeTab('crosssell');
	        $this->removeTab('categories');
	        $this->removeTab('related');
	        $this->removeTab('customer_options');
	        if (Mage::app()->getStore()->isCurrentlySecure()){
	            //echo "test";exit;
	            $link_category = Mage::getUrl('*/*/categories', array('_current' => true,'_secure'=>true));
	            $link_related = Mage::getUrl('*/*/related', array('_current' => true,'_secure'=>true));
	            $link_custom = Mage::getUrl('*/*/options', array('_current' => true,'_secure'=>true));
	            //echo $link_category;exit;
	        }
	        else{
	            $link_category = Mage::getUrl('*/*/categories', array('_current' => true));
	            $link_related = Mage::getUrl('*/*/related', array('_current' => true));
	            $link_custom = Mage::getUrl('*/*/options', array('_current' => true));
	        }
	        	
	    
	        $this->addTabAfter('categories', array(
	            'label'     => Mage::helper('vendorsproduct')->__('Main Categories'),
	            'url'       => $link_category,
	            'class'     => 'ajax',
	        ),"inventory");
	    
	        $this->addTabAfter('related', array(
	            'label'     => Mage::helper('catalog')->__('Related Products'),
	            'url'       => $link_related,
	            'class'     => 'ajax',
	        ),'categories');
	    
	        if (!$product->isGrouped()) {
	            $this->addTab('customer_options', array(
	                'label' => Mage::helper('catalog')->__('Custom Options'),
	                'url'   => $link_custom,
	                'class' => 'ajax',
	            ));
	        }
	    }
	    Mage::dispatchEvent('ves_vendorsproduct_product_edit_tabs_prepare_after',array('tabsblock'=>$this));
	}
    protected function _prepareLayout()
    {
        $product = $this->getProduct();
    
        if (!($setId = $product->getAttributeSetId())) {
            $setId = $this->getRequest()->getParam('set', null);
        }
    
        if ($setId) {
            $vendorProductSet = Mage::getModel('vendorsproduct/catalog_product_attribute_set')->load($setId,'parent_set_id');
            if(!$vendorProductSet->getId()) {
                parent::_prepareLayout();
                $this->removeTab('websites');
                $this->removeTab('upsell');
                $this->removeTab('crosssell');
                $this->removeTab('productalert');
                $this->removeTab('reviews');
                $this->removeTab('tags');
                $this->removeTab('customers_tags');
                $this->_prepareDefaultTabs();
				Mage::dispatchEvent('ves_vendorsproduct_product_edit_tabs_prepare_after',array('tabsblock'=>$this));
                return $this;
            }
            
            $groupCollection = Mage::getModel('vendorsproduct/entity_attribute_group')
                ->getCollection()
                ->addFieldToFilter('attribute_set_id',$vendorProductSet->getId())
                ->setOrder('sort_order','ASC')
                ->load();
            
            $displayType = $vendorProductSet->getData('group_display_type') ;
            
            if($displayType == VES_VendorsProduct_Model_Catalog_Product_Attribute_Set::DISPLAY_TYPE_FIELDSETS){
                $this->addTab('vendor_product_main', array(
                    'label'     => Mage::helper('vendorsproduct')->__('Product Information'),
                    'content'   => $this->_translateHtml($this->getLayout()->createBlock('vendorsproduct/vendor_product_edit_tab_fieldsetattr',
                        'adminhtml.catalog.product.edit.tab.fieldset.attributes')->setGroupCollection($groupCollection)
                        ->toHtml()),
                ));
            }else{
                foreach ($groupCollection as $group) {
                    // do not add groups without attributes
        
                    $entityAttributeCollection = Mage::getResourceModel('vendorsproduct/catalog_product_attribute_collection')
                        ->setAttributeGroupFilter($group->getId())
                        ->addVisibleFilter()
                        ->checkConfigurableProducts()
                        ->load();
        
                    if ($entityAttributeCollection->count()==0) {
                        continue;
                    }
        
                    $this->addTab('group_'.$group->getId(), array(
                        'label'     => Mage::helper('vendorsproduct')->__($group->getAttributeGroupName()),
                        'content'   => $this->_translateHtml($this->getLayout()->createBlock($this->getAttributeTabBlock(),
                            'adminhtml.catalog.product.edit.tab.attributes')->setGroup($group)
                            ->setGroupAttributes($entityAttributeCollection)
                            ->toHtml()),
                    ));
                }
            }
    
            if (Mage::helper('core')->isModuleEnabled('Mage_CatalogInventory')) {
                $this->addTab('inventory', array(
                    'label'     => Mage::helper('vendorsproduct')->__('Inventory'),
                    'content'   => $this->_translateHtml($this->getLayout()
                        ->createBlock('adminhtml/catalog_product_edit_tab_inventory')->toHtml()),
                ));
            }
    
    
            $this->addTab('categories', array(
                'label'     => Mage::helper('vendorsproduct')->__('Main Categories'),
                'url'       => $this->getUrl('*/*/categories', array('_current' => true)),
                'class'     => 'ajax',
            ));
    
            $this->addTab('related', array(
                'label'     => Mage::helper('vendorsproduct')->__('Related Products'),
                'url'       => $this->getUrl('*/*/related', array('_current' => true)),
                'class'     => 'ajax',
            ));

    
            /**
             * Do not change this tab id
             * @see Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs_Configurable
             * @see Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tabs
             */
            if (!$product->isGrouped()) {
                $this->addTab('customer_options', array(
                    'label' => Mage::helper('vendorsproduct')->__('Custom Options'),
                    'url'   => $this->getUrl('*/*/options', array('_current' => true)),
                    'class' => 'ajax',
                ));
            }
    
        }
        else {
            $this->addTab('set', array(
                'label'     => Mage::helper('vendorsproduct')->__('Settings'),
                'content'   => $this->_translateHtml($this->getLayout()
                    ->createBlock('vendorsproduct/vendor_product_edit_tab_settings')->toHtml()),
                'active'    => true
            ));
        }
		Mage::dispatchEvent('ves_vendorsproduct_product_edit_tabs_prepare_after',array('tabsblock'=>$this));
        return Mage_Adminhtml_Block_Widget_Tabs::_prepareLayout();
    }
    
    /**
     * Retrive product object from object if not from registry
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!($this->getData('product') instanceof Mage_Catalog_Model_Product)) {
            $this->setData('product', Mage::registry('product'));
        }
        return $this->getData('product');
    }
    
    /**
     * Getting attribute block name for tabs
     *
     * @return string
     */
    public function getAttributeTabBlock()
    {
        if (is_null(Mage::helper('adminhtml/catalog')->getAttributeTabBlock())) {
            return $this->_attributeTabBlock;
        }
        return Mage::helper('adminhtml/catalog')->getAttributeTabBlock();
    }
    
    public function setAttributeTabBlock($attributeTabBlock)
    {
        $this->_attributeTabBlock = $attributeTabBlock;
        return $this;
    }
    
    /**
     * Translate html content
     *
     * @param string $html
     * @return string
     */
    protected function _translateHtml($html)
    {
        Mage::getSingleton('core/translate_inline')->processResponseBody($html);
        return $html;
    }
}
