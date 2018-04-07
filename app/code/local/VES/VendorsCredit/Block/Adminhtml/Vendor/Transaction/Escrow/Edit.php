<?php

class VES_VendorsCredit_Block_Adminhtml_Vendor_Transaction_Escrow_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'vendorscredit';
        $this->_controller = 'adminhtml_vendor_transaction_escrow';
        $escrow = Mage::registry('current_escrow');
        
        if($escrow->canRelease()){
            $this->_updateButton('save', 'label', Mage::helper('vendorscredit')->__('Release'));
            $this->_updateButton('save', 'onclick', 'deleteConfirm(\''. Mage::helper('adminhtml')->__('Are you sure you want to do this?')
                    .'\', \'' . $this->getReleaseUrl() . '\')');
        }else{
            $this->_removeButton('save');
        }
        if($escrow->canCancel()){
            $this->_updateButton('delete', 'label', Mage::helper('vendorscredit')->__('Cancel'));
            $this->_updateButton('delete', 'onclick', 'deleteConfirm(\''. Mage::helper('adminhtml')->__('Are you sure you want to do this?')
                .'\', \'' . $this->getCancelUrl() . '\')');
        }else{
            $this->_removeButton('delete');
        }
        $this->_removeButton('reset');
    }

    public function getHeaderText()
    {
    	return Mage::helper('vendorscredit')->__('View Escrow Transaction');
    }
    
    /**
     * Get release URL.
     */
    public function getReleaseUrl(){
        return $this->getUrl('*/*/release',array('id'=>Mage::registry('current_escrow')->getId()));
    }
    
    /**
     * Get release URL.
     */
    public function getCancelUrl(){
        return $this->getUrl('*/*/cancel',array('id'=>Mage::registry('current_escrow')->getId()));
    }
}