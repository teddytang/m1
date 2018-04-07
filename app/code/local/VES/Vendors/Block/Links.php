<?php
class VES_Vendors_Block_Links extends Mage_Core_Block_Template
{
    /**
     * Add Vendor login link
     *
     * @return Mage_Checkout_Block_Links
     */
    public function addVendorLink()
    {
    	if(!Mage::helper('vendors')->moduleEnabled()){
    		return $this;
    	}

        $parentBlock = $this->getParentBlock();
        if ($parentBlock && Mage::helper('core')->isModuleOutputEnabled('VES_Vendors') && Mage::helper('vendors')->displayVendorUrlOnTopLink()) {
            $parentBlock->addLink(Mage::helper('vendors')->__('Vendor'),Mage::helper('vendors')->getStoreManageUrl(),Mage::helper('vendors')->__('Vendor'),null,null,10);
        }
        return $this;
    }

}
