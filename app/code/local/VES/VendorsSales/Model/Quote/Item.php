<?php
class VES_VendorsSales_Model_Quote_Item extends Mage_Sales_Model_Quote_Item
{
    /**
     * Check product representation in item
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  bool
     */
    public function representProduct($product)
    {

        if(Mage::helper('vendors')->getMode() != VES_Vendors_Model_Vendor::MODE_ADVANCED) return parent::representProduct($product);
        $itemProduct = $this->getProduct();
        if (!$product || $itemProduct->getId() != $product->getId()) {
            return false;
        }

        $transport = new Varien_Object(array('vendor_id'=>$product->getVendorId(),'item' => $this));
        Mage::dispatchEvent('ves_vendors_checkout_init_vendor_id',array('transport'=>$transport));
        $vendorId = $transport->getVendorId();

        if($transport->getPriceComparison() && $vendorId != $this->getVendorId()){
            return false;
        }
        /**
         * Check maybe product is planned to be a child of some quote item - in this case we limit search
         * only within same parent item
         */
        $stickWithinParent = $product->getStickWithinParent();
        if ($stickWithinParent) {
            if ($this->getParentItem() !== $stickWithinParent) {
                return false;
            }
        }

        // Check options
        $itemOptions = $this->getOptionsByCode();
        $productOptions = $product->getCustomOptions();

        if (!$this->compareOptions($itemOptions, $productOptions)) {
            return false;
        }
        if (!$this->compareOptions($productOptions, $itemOptions)) {
            return false;
        }
        return true;
    }
}