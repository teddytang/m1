<?php
class VES_VendorsSales_Model_Observer
{
	/**
	 * Send new order notification email to vendor
	 * @param Varien_Event_Observer $observer
	 */
	public function checkout_submit_all_after(Varien_Event_Observer $observer){
		$order = $observer->getOrder();
		if($order){
			Mage::helper('vendorssales')->sendNewOrderEmail($order);
		}
		$orders = $observer->getOrders();
		if($orders && is_array($orders)){
			foreach($orders as $order){
				Mage::helper('vendorssales')->sendNewOrderEmail($order);
			}
		}
	}
	
	public function sales_order_save_before(Varien_Event_Observer $observer){
		$order = $observer->getOrder();
		if(!$order->getVendorId() && ($quote = $order->getQuote())){
			$order->setVendorId($quote->getVendorId());
		}
	}
	
	public function sales_order_invoice_save_before(Varien_Event_Observer $observer){
    	$invoice	= $observer->getInvoice();
    	$order		= $invoice->getOrder();
    	$invoice->setVendorId($order->getVendorId());
    }
    
    public function sales_order_invoice_save_after(Varien_Event_Observer $observer){
    	$invoice	= $observer->getInvoice();
    	$order		= $invoice->getOrder();
    	
    }
    
    public function sales_order_shipment_save_before(Varien_Event_Observer $observer){
    	$shipment	= $observer->getShipment();
    	$order		= $shipment->getOrder();
    	$shipment->setVendorId($order->getVendorId());
    }
	
    public function sales_order_creditmemo_save_before(Varien_Event_Observer $observer){
    	$creditmemo	= $observer->getCreditmemo();
    	$order		= $creditmemo->getOrder();
    	$creditmemo->setVendorId($order->getVendorId());
    }
    
    public function ves_vendor_menu_check_acl(Varien_Event_Observer $observer){
    	$resource 	= $observer->getResource();
    	$result 	= $observer->getResult();
    	if(Mage::helper('vendors')->isAdvancedMode()){
	    	if($resource == 'vendors/sales/invoices' && !Mage::helper('vendorssales')->canSeeInvoice()){
	    		$result->setIsAllowed(false);
	    	}
	    	if($resource == 'vendors/sales/creditmemos' && !Mage::helper('vendorssales')->canSeeCreditMemo()){
	    		$result->setIsAllowed(false);
	    	}
    	}else{
    		if(in_array($resource, array('vendors/sales/invoices','vendors/sales/creditmemos','vendors/sales/shipments'))){
    			$result->setIsAllowed(false);
    		}
    	}
    }
}