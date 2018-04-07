<?php
/**
 * Adminhtml vendor attribute edit page tabs
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <core@magentocommerce.com>
 */
class VES_Vendors_Block_Adminhtml_Vendors_Attribute_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('vendor_attribute_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('vendors')->__('Attribute Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('main', array(
            'label'     => Mage::helper('vendors')->__('Properties'),
            'title'     => Mage::helper('vendors')->__('Properties'),
            'content'   => $this->getLayout()->createBlock('vendors/adminhtml_vendors_attribute_edit_tab_main')->toHtml(),
            'active'    => true
        ));

        $model = Mage::registry('entity_attribute');

        $this->addTab('labels', array(
            'label'     => Mage::helper('vendors')->__('Manage Label / Options'),
            'title'     => Mage::helper('vendors')->__('Manage Label / Options'),
            'content'   => $this->getLayout()->createBlock('vendors/adminhtml_vendors_attribute_edit_tab_options')->toHtml(),
        ));
        
        /*if ('select' == $model->getFrontendInput()) {
            $this->addTab('options_section', array(
                'label'     => Mage::helper('vendors')->__('Options Control'),
                'title'     => Mage::helper('vendors')->__('Options Control'),
                'content'   => $this->getLayout()->createBlock('adminhtml/catalog_product_attribute_edit_tab_options')->toHtml(),
            ));
        }*/

        return parent::_beforeToHtml();
    }

}
