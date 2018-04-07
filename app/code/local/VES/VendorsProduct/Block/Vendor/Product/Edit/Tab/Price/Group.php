<?php


class VES_VendorsProduct_Block_Vendor_Product_Edit_Tab_Price_Group
    extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_Group
{
    /**
     * Initialize block
     */
    public function __construct()
    {
        $this->setTemplate('ves_vendorsproduct/product/edit/price/group.phtml');
    }

    public function getAllCustomerGroupValue(){
        return Mage_Customer_Model_Group::CUST_GROUP_ALL;
    }
}
