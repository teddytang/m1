<?php
class VES_VendorsCheckout_Block_Onepage_Success extends Mage_Core_Block_Template
{
	protected function _prepareLayout(){
		/*Return if it's not advanced x mode*/
		if(Mage::helper('vendors')->getMode() != VES_Vendors_Model_Vendor::MODE_ADVANCED_X) return;
				
		$contentBlock = $this->getLayout()->getBlock('content');
		$contentBlock->unsetChild('checkout.success');
	}
	
	/**
     * Initialize data and prepare it for output
     */
    protected function _beforeToHtml()
    {
        $this->_prepareLastOrders();
        return parent::_beforeToHtml();
    }
    
    protected function _toHtml(){
    	if(Mage::helper('vendors')->getMode() != VES_Vendors_Model_Vendor::MODE_ADVANCED_X) return '';
    	return parent::_toHtml();
    }
	/**
     * Get last order ID from session, fetch it and check whether it can be viewed, printed etc
     */
    protected function _prepareLastOrders()
    {
        $orderIds 	= Mage::getSingleton('checkout/session')->getOrderIds();
        $orders 	= array();
        if($orderIds && is_array($orderIds)) foreach($orderIds as $orderId=>$orderIncrementId){
        	$order = Mage::getModel('sales/order')->load($orderId);
        	$orders[] = $order;
        }
    	$this->addData(array(
			'can_view_order'  	=> Mage::getSingleton('customer/session')->isLoggedIn(),
    		'orders'			=> $orders,
    		'order_ids'			=> $orderIds,
		));
    }
    
    public function getViewOrderUrl(Mage_Sales_Model_Order $order){
    	return $this->getUrl('sales/order/view/', array('order_id' => $order->getId()));
    }
    
}