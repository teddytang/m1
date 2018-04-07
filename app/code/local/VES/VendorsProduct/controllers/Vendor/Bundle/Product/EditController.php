<?php
require_once 'VES/VendorsProduct/controllers/Vendor/Catalog/ProductController.php';


class VES_VendorsProduct_Vendor_Bundle_Product_EditController extends VES_VendorsProduct_Vendor_Catalog_ProductController
{
	public function formAction()
    {
        $product = $this->_initProduct();
        echo $this->getLayout()->createBlock('bundle/adminhtml_catalog_product_edit_tab_bundle', 'admin.product.bundle.items')
                ->setProductId($product->getId())
                ->toHtml();
    }
}
