<?php

/**
 * Order history block
 *
 * @category   	VES
 * @package    	VES_Vendors
 * @author    	Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsSales_Block_Vendor_Sales_Invoice_View extends Mage_Core_Block_Template
{
    protected function _prepareLayout(){
    	parent::_prepareLayout();
    	//echo "test";exit;
    	$orderViewBlock = $this->getLayout()->getBlock('sales_invoice_view');
    	if($orderViewBlock){
	    	$invoice = $orderViewBlock->getInvoice();
	    	if ($invoice->canCapture()) {
	           $orderViewBlock->addButton('capture', array(
	                'label'     => Mage::helper('sales')->__('Capture'),
	                'class'     => 'save',
	                'onclick'   => 'setLocation(\''.$this->getCaptureUrl($invoice).'\')'
                )
         	   );
	        }
    	}
    	
    }
    public function getCaptureUrl($invoice){
    	 return $this->getUrl('*/*/capture',array('invoice_id'=>$invoice->getId()));
    }
    

    
}
