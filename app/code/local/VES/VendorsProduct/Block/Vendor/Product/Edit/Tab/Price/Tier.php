<?php
/**
 * Customer edit block
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsProduct_Block_Vendor_Product_Edit_Tab_Price_Tier extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_Tier
{
	/**
     * Initialize block
     */
    public function __construct()
    {
        $this->setTemplate('ves_vendorsproduct/product/edit/price/tier.phtml');
    }
    
    
    public function getAllCustomerGroupValue(){
    	return Mage_Customer_Model_Group::CUST_GROUP_ALL;
    }
}
