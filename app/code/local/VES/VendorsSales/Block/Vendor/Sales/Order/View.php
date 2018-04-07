<?php

/**
 * Order history block
 *
 * @category   	VES
 * @package    	VES_Vendors
 * @author    	Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsSales_Block_Vendor_Sales_Order_View extends Mage_Core_Block_Template
{
    protected function _prepareLayout(){
    	parent::_prepareLayout();
    	if(!Mage::helper('vendors')->isAdvancedMode()) return $this->_generalModePrepareLayout();
    	
    	$tabsBlock = $this->getLayout()->getBlock('sales_order_tabs');
    	if($tabsBlock){
    		$tabsBlock->addTabAfter('order_shipments_new', 'vendorssales/vendor_sales_order_view_tab_shipments','order_info');
			$tabsBlock->addTabAfter('order_invoice_new', 'vendorssales/vendor_sales_order_view_tab_invoices','order_info');
			$tabsBlock->addTabAfter('order_creditmemo_new', 'vendorssales/vendor_sales_order_view_tab_creditmemos','order_info');
	    	if(!Mage::getStoreConfig('vendors/sales/view_order_comments')){
	    		$tabsBlock->removeTab('order_history');
	    	}
	    	
	    	if(!Mage::helper('vendorssales')->canSeeInvoice()){
	    		$tabsBlock->removeTab('order_invoice_new');
	    	}
	    	if(!Mage::helper('vendorssales')->canSeeCreditMemo()){
	    		$tabsBlock->removeTab('order_creditmemo_new');
	    	}
	    	$tabsBlock->removeTab('order_invoices');
			$tabsBlock->removeTab('order_creditmemos');
	    	$tabsBlock->removeTab('order_shipments');
			
	    	//$tabsBlock->setTabData('order_shipments','block','vendorssales/vendor_sales_order_view_tab_shipments');
    	}
    	$orderViewBlock = $this->getLayout()->getBlock('sales_order_edit');
    	$orderViewBlock->removeButton('order_edit');
    	$orderViewBlock->removeButton('order_cancel');
    	$orderViewBlock->removeButton('order_creditmemo');
    	$orderViewBlock->removeButton('void_payment');
    	$orderViewBlock->removeButton('order_hold');
    	$orderViewBlock->removeButton('order_unhold');
    	$orderViewBlock->removeButton('accept_payment');
    	$orderViewBlock->removeButton('deny_payment');
    	$orderViewBlock->removeButton('order_invoice');
    	$orderViewBlock->removeButton('order_ship');
    	$orderViewBlock->removeButton('order_reorder');
    	
    	if($orderViewBlock){
	    	$order = $orderViewBlock->getOrder();
	    	if ($order->canShip()
	            && !$order->getForcedDoShipmentWithInvoice()) {
	            $orderViewBlock->addButton('order_ship', array(
	                'label'     => Mage::helper('sales')->__('Ship'),
	                'onclick'   => 'setLocation(\'' . $this->getShipUrl($order) . '\')',
	                'class'     => 'go'
	            ));
	        }
    	}
    	
    	$order = $orderViewBlock->getOrder();
    	
		
		if (Mage::getStoreConfig('vendors/sales/view_cancel')  && $order->canCancel()) {
            $message = Mage::helper('sales')->__('Are you sure you want to cancel this order?');
            $orderViewBlock->addButton('order_cancel', array(
                'label'     => Mage::helper('sales')->__('Cancel'),
                'onclick'   => 'deleteConfirm(\''.$message.'\', \'' . $this->getCancelUrl($order) . '\')',
            ));
        }


        if (Mage::getStoreConfig('vendors/sales/view_hold') && $order->canHold()) {
            $orderViewBlock->addButton('order_hold', array(
                'label'     => Mage::helper('sales')->__('Hold'),
                'onclick'   => 'setLocation(\'' . $this->getHoldUrl($order) . '\')',
            ));
        }

        if (Mage::getStoreConfig('vendors/sales/view_hold') && $order->canUnhold()) {
            $orderViewBlock->addButton('order_unhold', array(
                'label'     => Mage::helper('sales')->__('Unhold'),
                'onclick'   => 'setLocation(\'' . $this->getUnholdUrl($order) . '\')',
            ));
        }

		
		
    	if ( $order->canCreditmemo() && Mage::getStoreConfig('vendors/sales/view_creditmemo')) {
    		$message = Mage::helper('sales')->__('This will create an offline refund. To create an online refund, open an invoice and create credit memo for it. Do you wish to proceed?');
    		$onClick = "setLocation('{$this->getCreditmemoUrl($order)}')";
    		if ($order->getPayment()->getMethodInstance()->isGateway()) {
    			$onClick = "confirmSetLocation('{$message}', '{$this->getCreditmemoUrl($order)}')";
    		}
    		$orderViewBlock->addButton('order_creditmemo', array(
    				'label'     => Mage::helper('sales')->__('Credit Memo'),
    				'onclick'   => $onClick,
    				'class'     => 'go'
    		));
    	}

    	if ( $order->canInvoice() && Mage::getStoreConfig('vendors/sales/view_invoices')) {
    		$_label = $order->getForcedDoShipmentWithInvoice() ?
    		Mage::helper('sales')->__('Invoice and Ship') :
    		Mage::helper('sales')->__('Invoice');
    		$orderViewBlock->addButton('order_invoice', array(
    				'label'     => $_label,
    				'onclick'   => 'setLocation(\'' . $this->getInvoiceUrl($order) . '\')',
    				'class'     => 'go'
    		));
    	}
    }
    
    protected function _generalModePrepareLayout(){
    	parent::_prepareLayout();
    	$tabsBlock = $this->getLayout()->getBlock('sales_order_tabs');
    	/*Remove not allowed tabs*/
    	if($tabsBlock){
    		/*Add shipment tab*/
    		/*$tabsBlock->addTabAfter('order_shipments_new', 'vendorssales/vendor_sales_order_view_tab_shipments','order_info');*/
    		$tabsBlock->removeTab('order_history');
    		$tabsBlock->removeTab('order_invoices');
    		$tabsBlock->removeTab('order_creditmemos');
    		$tabsBlock->removeTab('order_shipments');
    	}
    	
    	/*Add ship button*/
    	/*
    	$orderViewBlock = $this->getLayout()->getBlock('sales_order_edit');
    	if($orderViewBlock){
	    	$order = $orderViewBlock->getOrder();
	    	if ($order->canShip()
	            && !$order->getForcedDoShipmentWithInvoice()) {
	            $orderViewBlock->addButton('order_ship', array(
	                'label'     => Mage::helper('sales')->__('Ship'),
	                'onclick'   => 'setLocation(\'' . $this->getShipUrl($order) . '\')',
	                'class'     => 'go'
	            ));
	        }
    	}
    	*/
    	$itemBlock = $this->getLayout()->getBlock('order_items');
    	if($itemBlock){
    		$itemBlock->setTemplate('ves_vendorssales/order/view/items.phtml');
    	}
    	return $this;    	
    }
	
	
	
    public function getCancelUrl($order){
        return $this->getUrl('*/*/cancel',array('order_id'=>$order->getId()));
    }

    public function getHoldUrl($order){
        return $this->getUrl('*/*/hold',array('order_id'=>$order->getId()));
    }
    public function getUnholdUrl($order){
        return $this->getUrl('*/*/unhold',array('order_id'=>$order->getId()));
    }

	
	
    public function getShipUrl($order){
    	 return $this->getUrl('*/sales_shipment/start',array('order_id'=>$order->getId()));
    }
    
    public function getInvoiceUrl($order)
    {
    	return $this->getUrl('*/sales_order_invoice/start',array('order_id'=>$order->getId()));
    }
    
    public function getCreditmemoUrl($order)
    {
    	return $this->getUrl('*/sales_order_creditmemo/start',array('order_id'=>$order->getId()));
    }
    
    protected function _isAllowedAction($action)
    {
    	return Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/' . $action);
    }
    
    
}
