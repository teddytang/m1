<?php
class VES_VendorsCheckout_Model_Observer
{
	
	/**
     * Get Checkout session
     */
    public function getCheckoutSession(){
    	return Mage::getSingleton('checkout/session');
    }
	
    /**
     * After the product is added to quote, check the vendor id and assign the product to corresponding 
     * quote based on vendor id.
     * @param Varien_Event_Observer $observer
     */
    public function sales_quote_product_add_after(Varien_Event_Observer $observer){
        /*Do nothing if the extension is disabled*/
        if(!Mage::helper('vendors')->moduleEnabled()) return;
        
    	if(Mage::helper('vendors')->getMode() != VES_Vendors_Model_Vendor::MODE_ADVANCED) return;
    	
    	$items = $observer->getItems();

    	$quote = Mage::getSingleton('checkout/session')->getQuote();
    	$vendorsCheckoutSession = Mage::getSingleton('vendorscheckout/session');
    	foreach($items as $item){

    		/*Do not process if the items from a vendor quote*/
    		$vendorId = $item->getProduct()->getVendorId();

    		$transport = new Varien_Object(array('vendor_id'=>$vendorId,'item'=>$item ,'pricecomparison'=>false));
    		Mage::dispatchEvent('ves_vendors_checkout_init_vendor_id',array('transport'=>$transport));
    		$vendorId = $transport->getVendorId();

    		if($item->getQuote()->getVendorId() && ($vendorId == $item->getQuote()->getVendorId()) && !$transport->getPriceComparison()){
				$vendorsCheckoutSession->setQuoteId($item->getQuote()->getVendorId() ,$item->getQuote()->getId() );
				return;
			}

    		if($vendorId){
    			$newQuote = $vendorsCheckoutSession->getQuote($vendorId);
				$cart 	= Mage::getModel('vendorscheckout/cart')->setData('quote',$newQuote);
    			$found = false;

	    		if(!$newQuote->getId()) {
	    			/*Save quote if it's not saved*/
	            	$cart->save();
	            	$newQuote = $cart->getQuote();
					$item->setQuote($newQuote)->save();
					if ($item->getHasChildren()) {
						foreach ($item->getChildren() as $child) {
							$child->setQuote($newQuote)->save();
						}
					}
	            }else{
	            
	            	/*Check if the item is already exist on the quote*/
	    			foreach ($newQuote->getAllVisibleItems() as $quoteItem) {
		                 if ($quoteItem->compare($item)) {

		                	$item->setQuote($newQuote);
		                    $item->addQty($quoteItem->getQty());
		                    $item->save();
		                    $newQuote->removeItem($quoteItem->getId());

		                    $found = true;		        
		                    break;
		                }
	    			}

	    			if (!$found) {
    				/*If item is not exist on the quote*/
		                $item->setQuote($newQuote)->save();
		                if ($item->getHasChildren()) {
		                    foreach ($item->getChildren() as $child) {
		                        $child->setQuote($newQuote)->save();
		                    }
		                }
	           	    }
	            }

	            $cart->save();
    			
    		}

    	}
    }
    
    /**
     * Set the quote of default quote to new checkout session
     * @param Varien_Event_Observer $observer
     */
    public function checkout_cart_save_after(Varien_Event_Observer $observer){
        /*Do nothing if the extension is disabled*/
        if(!Mage::helper('vendors')->moduleEnabled()) return;
        
    	$cart = $observer->getCart();
    	Mage::getSingleton('vendorscheckout/session')->setQuoteId(0,$cart->getQuote()->getId());
    }
    
    /**
     * Remove old shopping cart if the extension mode is advanced
     * @param Varien_Event_Observer $observer
     */
    public function controller_action_layout_load_before(Varien_Event_Observer $observer){
        /*Do nothing if the extension is disabled*/
        if(!Mage::helper('vendors')->moduleEnabled()) return;
        
    	if(Mage::helper('vendors')->getMode() != VES_Vendors_Model_Vendor::MODE_ADVANCED) return;
    	$request = $observer->getEvent()->getAction()->getRequest();
    	$delimiter = '_';
    	$actionName = $request->getRequestedRouteName().$delimiter.
            $request->getRequestedControllerName().$delimiter.
            $request->getRequestedActionName();
            
    	if($actionName == 'checkout_cart_index'){
    		$observer->getEvent()->getLayout()->getUpdate()->addHandle('vendorscheckout_advanced_mode');
    	}
    }
    
    
	/**
	 * - replace quote if it's child quote
	 * @param Varien_Event_Observer $observer
	 */
	public function controller_action_predispatch(Varien_Event_Observer $observer){
	    /*Do nothing if the extension is disabled*/
	    if(!Mage::helper('vendors')->moduleEnabled()) return;
	    
		/*Return if it's not advanced mode*/
		if(Mage::helper('vendors')->getMode() != VES_Vendors_Model_Vendor::MODE_ADVANCED) return;

		$controllerAction = $observer->getControllerAction();
		$routeName = $controllerAction->getRequest()->getRouteName();
		if($routeName == 'checkout' && $controllerAction->getRequest()->getControllerName() == 'onepage' || $routeName = 'paypal') return;
		
		/*return to parent quote*/
		$quote = Mage::getSingleton('checkout/session')->getQuote();
		
		if($parentQuoteId = $quote->getParentQuote()){
			$parentQuote = Mage::getModel('sales/quote')->load($parentQuoteId);
			Mage::getSingleton('checkout/session')->replaceQuote($parentQuote);
		}

	}
	/**
	 * - Init vendor quotes
	 * @param Varien_Event_Observer $observer
	 */
	public function checkout_cart_index(Varien_Event_Observer $observer){
	    /*Do nothing if the extension is disabled*/
	    if(!Mage::helper('vendors')->moduleEnabled()) return;
	    
		/*Return if it's not advanced mode*/
		if(Mage::helper('vendors')->getMode() != VES_Vendors_Model_Vendor::MODE_ADVANCED) return;

		$quotes = Mage::getSingleton('vendorscheckout/session')->getQuotes();

		foreach($quotes as $quote){
			if(!$quote->getVendorId()) continue;

			$cart 	= Mage::getModel('vendorscheckout/cart')->setData('quote',$quote);
			$cart->init();
			
			if(!$quote->getParentQuote() || $quote->getParentQuote() != Mage::getSingleton('checkout/session')->getQuote()->getId()){
				$quote->setParentQuote(Mage::getSingleton('checkout/session')->getQuote()->getId());
			}
			
			$cart->save();
		}
	}
	
	public function customer_account_loginPost(Varien_Event_Observer $observer){
	    $this->checkout_cart_index($observer);
	}
    /**
     * Edit cart item from vendor quote
     * @param Varien_Event_Observer $observer
     */
	public function checkout_cart_configure(Varien_Event_Observer $observer){
	    /*Do nothing if the extension is disabled*/
	    if(!Mage::helper('vendors')->moduleEnabled()) return;
	    
		/*Return if it's not advanced mode*/
		if(Mage::helper('vendors')->getMode() != VES_Vendors_Model_Vendor::MODE_ADVANCED) return;
		
		$vendorQuoteSession = Mage::getSingleton('vendorscheckout/session');
		$action = $observer->getControllerAction();
		
		// Extract item and product to configure
        $id = (int) $action->getRequest()->getParam('id');
        $quoteItem = null;
        
		foreach($vendorQuoteSession->getQuotes() as $quote){
			if(!$quote->getVendorId()) continue;
			
			$quoteItem = $quote->getItemById($id);
			if($quoteItem) break;
		}
		
        if (!$quoteItem) return;

        try {
            $params = new Varien_Object();
            $params->setCategoryId(false);
            $params->setConfigureMode(true);
            $params->setBuyRequest($quoteItem->getBuyRequest());

            Mage::helper('catalog/product_view')->prepareAndRender($quoteItem->getProduct()->getId(), $action, $params);
        } catch (Exception $e) {
            return;
        }
        $action->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH,true);
	}
	
	/**
	 * Save the item info (from edit item page)
	 * @param Varien_Event_Observer $observer
	 */
	public function checkout_cart_updateItemOptions(Varien_Event_Observer $observer){
	    /*Do nothing if the extension is disabled*/
	    if(!Mage::helper('vendors')->moduleEnabled()) return;
	    
	    
		/*Return if it's not advanced mode*/
		if(Mage::helper('vendors')->getMode() != VES_Vendors_Model_Vendor::MODE_ADVANCED) return;
		
		$vendorQuoteSession = Mage::getSingleton('vendorscheckout/session');
		$checkoutSession 	= Mage::getSingleton('checkout/session');
		$action = $observer->getControllerAction();
		
		
        $id = (int) $action->getRequest()->getParam('id');
        $params = $action->getRequest()->getParams();

        if (!isset($params['options'])) {
            $params['options'] = array();
        }
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $quoteItem = null;
        	$vendorQuote = null;
			foreach($vendorQuoteSession->getQuotes() as $quote){
				if(!$quote->getVendorId()) continue;
				
				$quoteItem = $quote->getItemById($id);
				$vendorQuote = $quote;
				if($quoteItem) break;
			}
			/*Return if the quote item í not exist on vendor quotes.*/
	        if (!$quoteItem) return;
	        $cart 	= Mage::getModel('vendorscheckout/cart')->setData('quote',$vendorQuote);
            $item 	= $cart->updateItem($id, new Varien_Object($params));
            if (is_string($item)) {
                Mage::throwException($item);
            }
            if ($item->getHasError()) {
                Mage::throwException($item->getMessage());
            }

            $related = $action->getRequest()->getParam('related_product');
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }

            $cart->save();


            Mage::dispatchEvent('checkout_cart_update_item_complete',
                array('item' => $item, 'request' => $action->getRequest(), 'response' => $action->getResponse())
            );
            if (!$checkoutSession->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError()) {
                    $message = $action->__('%s was updated in your shopping cart.', Mage::helper('core')->escapeHtml($item->getProduct()->getName()));
                    $checkoutSession->addSuccess($message);
                }
                $action->setRedirectWithCookieCheck('checkout/cart');
            }
        } catch (Mage_Core_Exception $e) {
            if ($checkoutSession->getUseNotice(true)) {
                $checkoutSession->addNotice($e->getMessage());
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $checkoutSession->addError($message);
                }
            }

            $url = $checkoutSession->getRedirectUrl(true);
            if ($url) {
                $action->getResponse()->setRedirect($url);
            } else {
                $action->getResponse()->setRedirect(Mage::helper('checkout/cart')->getCartUrl());
            }
        } catch (Exception $e) {
            $checkoutSession->addException($e, $action->__('Cannot update the item.'));
            Mage::logException($e);
            $action->setRedirectWithCookieCheck('checkout/cart');
        }
        $action->setRedirectWithCookieCheck('checkout/cart');
        
        $action->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH,true);
	}
	
	/**
	 * Remove item from shopping cart.
	 * @param Varien_Event_Observer $observer
	 */
	public function checkout_cart_delete(Varien_Event_Observer $observer){
	    /*Do nothing if the extension is disabled*/
	    if(!Mage::helper('vendors')->moduleEnabled()) return;
	    
	    
		/*Return if it's not advanced mode*/
		if(Mage::helper('vendors')->getMode() != VES_Vendors_Model_Vendor::MODE_ADVANCED) return;
		
		$vendorQuoteSession = Mage::getSingleton('vendorscheckout/session');
		$checkoutSession 	= Mage::getSingleton('checkout/session');
		$action = $observer->getControllerAction();
		

		$id = (int) $action->getRequest()->getParam('id');
		if(!$id) return;

		$quoteItem 		= null;
		$vendorQuote 	= null;
		foreach($vendorQuoteSession->getQuotes() as $quote){
			if(!$quote->getVendorId()) continue;
			
			$quoteItem 		= $quote->getItemById($id);
			$vendorQuote 	= $quote;
			if($quoteItem) break;
		}
		
		/*Return if the quote item í not exist on vendor quotes.*/
		if (!$quoteItem) return;
		
		
		$cart = Mage::getModel('vendorscheckout/cart')->setData('quote',$vendorQuote);
		try {
			$cart->removeItem($id)->save();
		} catch (Exception $e) {
			$checkoutSession->addError($action->__('Cannot remove the item.'));
			Mage::logException($e);
		}

        $action->setRedirectWithCookieCheck('checkout/cart');
        $action->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH,true);
	}
	
	
	/**
	 * Merge quote when customer login.
	 * @param Varien_Event_Observer $observer
	 */
	public function checkout_cart_updatePost(Varien_Event_Observer $observer){
	    /*Do nothing if the extension is disabled*/
	    if(!Mage::helper('vendors')->moduleEnabled()) return;
	    
	    
		/*Return if it's not advanced mode*/
		if(Mage::helper('vendors')->getMode() != VES_Vendors_Model_Vendor::MODE_ADVANCED) return;
		
		$action 	= $observer->getControllerAction();
		$vendorId 	= $action->getRequest()->getParam('vendor_id',false);
		if(!$vendorId) return;
		
		$vendor = Mage::getModel('vendors/vendor')->load($vendorId);
		if(!$vendor->getId()) return;
		
		/*Validate form key*/
		if (!($formKey = $action->getRequest()->getParam('form_key', null))
            || $formKey != Mage::getSingleton('core/session')->getFormKey()) {
            return;
        }
        
        $updateAction = (string)$action->getRequest()->getParam('update_cart_action');
        
		switch ($updateAction) {
            case 'empty_cart':
                $this->_emptyShoppingCart($vendor,$action);
                break;
            case 'update_qty':
                $this->_updateShoppingCart($vendor,$action);
                break;
            default:
                $this->_updateShoppingCart($vendor,$action);
        }
        
        $action->setRedirectWithCookieCheck('checkout/cart');
        $action->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH,true);
	}
	
	
	/**
	 * Add coupon code to quote
	 * @param Varien_Event_Observer $observer
	 */
	public function checkout_cart_couponPost(Varien_Event_Observer $observer){
	    /*Do nothing if the extension is disabled*/
	    if(!Mage::helper('vendors')->moduleEnabled()) return;
	    
	    
		$vendorQuoteSession = Mage::getSingleton('vendorscheckout/session');
		$checkoutSession 	= Mage::getSingleton('checkout/session');
		$action 	= $observer->getControllerAction();
		$vendorId 	= $action->getRequest()->getParam('vendor');
		
		if(!$vendorId) return;
		
		$vendorQuote 	= $vendorQuoteSession->getQuote($vendorId);
		$cart 			= Mage::getModel('vendorscheckout/cart')->setData('quote',$vendorQuote);
		
		
		if (!$vendorQuote->getItemsCount()) {
            return;
        }

        $couponCode = (string) $action->getRequest()->getParam('coupon_code');
        if ($action->getRequest()->getParam('remove') == 1) {
            $couponCode = '';
        }
        $oldCouponCode = $vendorQuote->getCouponCode();

        if (!strlen($couponCode) && !strlen($oldCouponCode)) {
            return;
        }

        try {
            $codeLength = strlen($couponCode);
            $isCodeLengthValid = $codeLength && $codeLength <= Mage_Checkout_Helper_Cart::COUPON_CODE_MAX_LENGTH;

            $vendorQuote->getShippingAddress()->setCollectShippingRates(true);
            $vendorQuote->setCouponCode($isCodeLengthValid ? $couponCode : '')
                ->collectTotals()
                ->save();

            if ($codeLength) {
                if ($isCodeLengthValid && $couponCode == $vendorQuote->getCouponCode()) {
                    $checkoutSession->addSuccess(
                        $action->__('Coupon code "%s" was applied.', Mage::helper('core')->escapeHtml($couponCode))
                    );
                } else {
                    $checkoutSession->addError(
                        $action->__('Coupon code "%s" is not valid.', Mage::helper('core')->escapeHtml($couponCode))
                    );
                }
            } else {
                $checkoutSession->addSuccess($action->__('Coupon code was canceled.'));
            }

        }catch (Exception $e) {
            return;
        }

		$action->setRedirectWithCookieCheck('checkout/cart');
        $action->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH,true);
	}
	
	/**
	 * Shipping estimate post.
	 * @param Varien_Event_Observer $observer
	 */
	public function checkout_cart_estimatePost(Varien_Event_Observer $observer){
	    /*Do nothing if the extension is disabled*/
	    if(!Mage::helper('vendors')->moduleEnabled()) return;
	    
	    
	    $vendorQuoteSession = Mage::getSingleton('vendorscheckout/session');
	    $checkoutSession 	= Mage::getSingleton('checkout/session');
	    $action 	= $observer->getControllerAction();
	    $vendorId 	= $action->getRequest()->getParam('vendor');
	    
	    if(!$vendorId) return;
	    
	    $country    = (string) $action->getRequest()->getParam('country_id');
	    $postcode   = (string) $action->getRequest()->getParam('estimate_postcode');
	    $city       = (string) $action->getRequest()->getParam('estimate_city');
	    $regionId   = (string) $action->getRequest()->getParam('region_id');
	    $region     = (string) $action->getRequest()->getParam('region');
	    
	    $vendorQuote   = $vendorQuoteSession->getQuote($vendorId);
	    
	    $vendorQuote->getShippingAddress()
	    ->setCountryId($country)
	    ->setCity($city)
	    ->setPostcode($postcode)
	    ->setRegionId($regionId)
	    ->setRegion($region)
	    ->setCollectShippingRates(true);
	    $vendorQuote->save();
	    
	    $action->setRedirectWithCookieCheck('checkout/cart');
	    $action->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH,true);
	}
	
	/**
	 * Shipping estimate update post
	 * @param Varien_Event_Observer $observer
	 */
	public function checkout_cart_estimateUpdatePost(Varien_Event_Observer $observer){
	    /*Do nothing if the extension is disabled*/
	    if(!Mage::helper('vendors')->moduleEnabled()) return;
	    
	    
	    $vendorQuoteSession = Mage::getSingleton('vendorscheckout/session');
	    $checkoutSession 	= Mage::getSingleton('checkout/session');
	    $action 	= $observer->getControllerAction();
	    $vendorId 	= $action->getRequest()->getParam('vendor');
	     
	    if(!$vendorId) return;
	    $vendorQuote   = $vendorQuoteSession->getQuote($vendorId);
        $code = (string) $action->getRequest()->getParam('estimate_method');
        if (!empty($code)) {
            $vendorQuote->getShippingAddress()->setShippingMethod($code)/*->collectTotals()*/->save();
        }
        
	    $action->setRedirectWithCookieCheck('checkout/cart');
	    $action->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH,true);
	}
	
	/**
     * Retrieve wishlist object
     * @param Mage_Core_Controller_Front_Action $action
     * @return Mage_Wishlist_Model_Wishlist|bool
     */
    protected function _getWishlist(Mage_Core_Controller_Front_Action $action)
    {
        /*Do nothing if the extension is disabled*/
        if(!Mage::helper('vendors')->moduleEnabled()) return;
        
        
        $wishlist = Mage::registry('wishlist');
        if ($wishlist) {
            return $wishlist;
        }

        try {

            $customerId = Mage::getSingleton('customer/session')->getCustomerId();
            /* @var Mage_Wishlist_Model_Wishlist $wishlist */
            $wishlist = Mage::getModel('wishlist/wishlist');

            $wishlist->loadByCustomer($customerId, true);

            if (!$wishlist->getId() || $wishlist->getCustomerId() != $customerId) {
                $wishlist = null;
                Mage::throwException(
                    Mage::helper('wishlist')->__("Requested wishlist doesn't exist")
                );
            }

            Mage::register('wishlist', $wishlist);
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('wishlist/session')->addError($e->getMessage());
            return false;
        } catch (Exception $e) {
            Mage::getSingleton('wishlist/session')->addException($e,
                Mage::helper('wishlist')->__('Wishlist could not be created.')
            );
            return false;
        }

        return $wishlist;
    }
	
	/**
	 * Move quote item to wishlist
	 * @param Varien_Event_Observer $observer
	 */
	public function wishlist_index_fromcart(Varien_Event_Observer $observer){
	    /*Do nothing if the extension is disabled*/
	    if(!Mage::helper('vendors')->moduleEnabled()) return;
	    
	    
		/*Return if it's not advanced mode*/
		if(Mage::helper('vendors')->getMode() != VES_Vendors_Model_Vendor::MODE_ADVANCED) return;
		
		$action 	= $observer->getControllerAction();
		$vendorQuoteSession = Mage::getSingleton('vendorscheckout/session');
		$checkoutSession 	= Mage::getSingleton('checkout/session');
		
		$wishlist = $this->_getWishlist();
        if (!$wishlist) {
            return $action->norouteAction();
        }
        $itemId = (int) $action->getRequest()->getParam('item');
		if(!$itemId) return;

		$quoteItem 		= null;
		$vendorQuote 	= null;
		foreach($vendorQuoteSession->getQuotes() as $quote){
			if(!$quote->getVendorId()) continue;
			
			$quoteItem 		= $quote->getItemById($itemId);
			$vendorQuote 	= $quote;
			if($quoteItem) break;
		}
		
		/*Return if the quote item í not exist on vendor quotes.*/
		if (!$quoteItem) return;
		
		
		$cart = Mage::getModel('vendorscheckout/cart')->setData('quote',$vendorQuote);
		
        try {
            $productId  = $quoteItem->getProductId();
            $buyRequest = $quoteItem->getBuyRequest();

            $wishlist->addNewItem($productId, $buyRequest);

            $productIds[] = $productId;
            $cart->getQuote()->removeItem($itemId);
            $cart->save();
            Mage::helper('wishlist')->calculate();
            $productName = Mage::helper('core')->escapeHtml($quoteItem->getProduct()->getName());
            $wishlistName = Mage::helper('core')->escapeHtml($wishlist->getName());
            $checkoutSession->addSuccess(
                Mage::helper('wishlist')->__("%s has been moved to wishlist %s", $productName, $wishlistName)
            );
            $wishlist->save();
        } catch (Mage_Core_Exception $e) {
            $checkoutSession->addError($e->getMessage());
        } catch (Exception $e) {
            $checkoutSession->addException($e, Mage::helper('wishlist')->__('Cannot move item to wishlist'));
        }
		$action->setRedirectWithCookieCheck('checkout/cart');
        $action->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH,true);
	}
	/**
     * Empty customer's shopping cart
     */
    protected function _emptyShoppingCart(VES_Vendors_Model_Vendor $vendor)
    {
        /*Do nothing if the extension is disabled*/
        if(!Mage::helper('vendors')->moduleEnabled()) return;
        
        
        try {
        	$session 			= Mage::getSingleton('checkout/session');
        	$checkoutSession 	= Mage::getSingleton('vendorscheckout/session');
        	/*Get quote by vendor id*/
        	$quote = $checkoutSession->getQuote($vendor->getId());
	        
	        $cart 	= Mage::getModel('vendorscheckout/cart')->setData('quote',$quote);
	        $cart->truncate()->save();
            $session->setCartWasUpdated(true);
            
        } catch (Mage_Core_Exception $exception) {
            $session->addError($exception->getMessage());
        } catch (Exception $exception) {
            $session->addException($exception, $this->__('Cannot update shopping cart.'));
        }
    }
    
	/**
     * Update customer's shopping cart
     */
    protected function _updateShoppingCart(VES_Vendors_Model_Vendor $vendor,$action)
    {
        /*Do nothing if the extension is disabled*/
        if(!Mage::helper('vendors')->moduleEnabled()) return;
        
        
        try {
        	$session 			= Mage::getSingleton('checkout/session');
        	$checkoutSession 	= Mage::getSingleton('vendorscheckout/session');
            $cartData = $action->getRequest()->getParam('cart');
            if (is_array($cartData)) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                foreach ($cartData as $index => $data) {
                    if (isset($data['qty'])) {
                        $cartData[$index]['qty'] = $filter->filter(trim($data['qty']));
                    }
                }

                /*Get quote by vendor id*/
        		$quote 	= $checkoutSession->getQuote($vendor->getId());
                $cart 	= Mage::getModel('vendorscheckout/cart')->setData('quote',$quote);
                if (! $cart->getCustomerSession()->getCustomer()->getId() && $quote->getCustomerId()) {
                    $quote->setCustomerId(null);
                }

                $cartData = $cart->suggestItemsQty($cartData);
                $cart->updateItems($cartData)
                    ->save();
            }
            $session->setCartWasUpdated(true);
        } catch (Mage_Core_Exception $e) {
            $session->addError(Mage::helper('core')->escapeHtml($e->getMessage()));
        } catch (Exception $e) {
            $session->addException($e, $this->__('Cannot update shopping cart.'));
            Mage::logException($e);
        }
    }

    
	/**
	 * Set Vendor Id for Quote Item if it's not exist
	 * @param Varien_Event_Observer $observer
	 */
	public function sales_quote_item_save_before(Varien_Event_Observer $observer){
	    /*Do nothing if the extension is disabled*/
	    if(!Mage::helper('vendors')->moduleEnabled()) return;
	    
	    
		$item = $observer->getItem();
		if(!$item->getVendorId()) $item->setVendorId($item->getProduct()->getVendorId());      
	}
	
	/**
	 * Set Vendor Id for Quote Item if it's not exist
	 * @param Varien_Event_Observer $observer
	 */
	public function sales_order_item_save_before(Varien_Event_Observer $observer){
	    /*Do nothing if the extension is disabled*/
	    if(!Mage::helper('vendors')->moduleEnabled()) return;
	    
	    
		$item = $observer->getItem();
		
		$vendorId = $item->getProduct()->getVendorId();
		$transport = new Varien_Object(array('vendor_id'=>$vendorId,'item'=>$item));
		Mage::dispatchEvent('ves_vendors_checkout_init_vendor_id',array('transport'=>$transport));
		$vendorId = $transport->getVendorId();
		
		if(!$item->getVendorId()) $item->setVendorId($vendorId);      
	}
	
	/**
	 * Onepage checkout
	 * - Create new temp quote for current vendor
	 * - Create items of current vendor and relate it to temp quote.
	 * 
	 * @param Varien_Event_Observer $observer
	 */
	public function checkout_onepage_index(Varien_Event_Observer $observer){
	    /*Do nothing if the extension is disabled*/
	    if(!Mage::helper('vendors')->moduleEnabled()) return;
	    
	    
		/*Return if it's not advanced mode*/
		if(Mage::helper('vendors')->getMode() != VES_Vendors_Model_Vendor::MODE_ADVANCED) return;

		$action 			= $observer->getControllerAction();
		$vendorId 			= $action->getRequest()->getParam('vendor');
		$checkoutSession 	= Mage::getSingleton('checkout/session');
		
		/*If current quote is vendor quote just get the checkout session quote*/
		if(!$vendorId) {
			$quote = $checkoutSession->getQuote();
			if($parentQuoteId = $quote->getParentQuote()){
				$parentQuote = Mage::getModel('sales/quote')->load($parentQuoteId);
				$checkoutSession->replaceQuote($parentQuote);
			}
			return;
		}
		
		$vendor 	= Mage::getModel('vendors/vendor')->load($vendorId);
		/* If vendor is not exist redirect to noRoute */
		if(!$vendor->getId()){
			Mage::getSingleton('checkout/session')->addError('You are not allowed to access this page.');
			$action->setFlag('', 'no-dispatch', true);
			$action->setRedirectWithCookieCheck('checkout/cart');
			return;
		}
		
		/*Replace the current quote by vendor quote*/
		$vendorQuote = Mage::getSingleton('vendorscheckout/session')->getQuote($vendorId);		
		if(!$vendorQuote->getParentQuote()){
			$vendorQuote->setParentQuote($checkoutSession->getQuote()->getId())->save();
		}
		$checkoutSession->replaceQuote($vendorQuote);
	}
	

	
	
	/**
	 * Unset the vendor quote id (Advanced Mode).
	 * Return new success page on ADVANCED X mode
	 * @param Varien_Event_Observer $observer
	 */
	public function checkout_onepage_success_predispatch(Varien_Event_Observer $observer){
	    /*Do nothing if the extension is disabled*/
	    if(!Mage::helper('vendors')->moduleEnabled()) return;
	    
	    		
		
		if(Mage::helper('vendors')->getMode() == VES_Vendors_Model_Vendor::MODE_ADVANCED){
			/*Return if it's not advanced mode*/
			$checkoutSession 	= Mage::getSingleton('checkout/session');
			$lastQuoteId 		= $checkoutSession->getLastQuoteId();
			$lastQuote			= Mage::getModel('sales/quote')->load($lastQuoteId);
			$vendorsCheckoutSession 	= Mage::getSingleton('vendorscheckout/session');
			
			$vendorsCheckoutSession->setQuoteId($lastQuote->getVendorId(),null);
			$checkoutSession->setQuoteId(null);
			if($lastQuote->getVendorId()){
				$checkoutSession->replaceQuote($vendorsCheckoutSession->getQuote(0));
			}
		}
        
        
		/*Return if it's not advanced mode*/
		if(Mage::helper('vendors')->getMode() != VES_Vendors_Model_Vendor::MODE_ADVANCED_X) return;
		$session = Mage::getSingleton('checkout/session');
		$controllerAction = $observer->getControllerAction();
		$lastOrderIds = $session->getOrderIds();
		if(is_array($lastOrderIds) && sizeof($lastOrderIds)){
			$controllerAction->setFlag('', 'no-dispatch', true);
			$controllerAction->getRequest()->setDispatched(true);
			$controllerAction->loadLayout();
			$controllerAction->renderLayout();
			$session->clear();
			$session->unsetData('order_ids');
			Mage::dispatchEvent('checkout_onepage_controller_success_action', array('order_ids' => $lastOrderIds));
		}
	}
	
	/**
	* fired before controller ajax cart update
	*
	**/
	public function controller_action_predispatch_checkout_cart_ajaxUpdate($ob) {
		if(!Mage::helper('vendors')->moduleEnabled()) return;
		
		if(Mage::helper('vendors')->getMode() != VES_Vendors_Model_Vendor::MODE_ADVANCED) return;
		
		$action = $ob->getControllerAction();
		
		$id = (int)Mage::app()->getRequest()->getParam('id'); //id quote item
        $qty = Mage::app()->getRequest()->getParam('qty');	//qty
        $result = array();
		$session 			= Mage::getSingleton('checkout/session');
		$checkoutSession 	= Mage::getSingleton('vendorscheckout/session');
		$cart 				= Mage::getSingleton('vendorscheckout/cart');
		if($id) {
			try {
				if (isset($qty)) {
                    $filter = new Zend_Filter_LocalizedToNormalized(
                        array('locale' => Mage::app()->getLocale()->getLocaleCode())
                    );
                    $qty = $filter->filter($qty);
                }
				$quoteItem 		= null;
				$vendorQuote 	= null;
				foreach($checkoutSession->getQuotes() as $quote){
					if(!$quote->getVendorId()) continue;
					
					$quoteItem 		= $quote->getItemById($id);
					$vendorQuote 	= $quote;
					if($quoteItem) break;
				}
				$quoteItem->setQty($qty)->save();
				
				if (! $cart->getCustomerSession()->getCustomer()->getId() && $quote->getCustomerId()) {
                    $vendorQuote->setCustomerId(null);
                }
                
				$cart->save();
				
				/**load layout**/
				$action->loadLayout();
                $result['content'] = $action->getLayout()->getBlock('minicart_content')->toHtml();

                $result['qty'] = $cart->getSummaryQty();

                if (!$quoteItem->getHasError()) {
                    $result['message'] = Mage::helper('core')->__('Item was updated successfully.');
                } else {
                    $result['notice'] = $quoteItem->getMessage();
                }
                $result['success'] = 1;
			}
			catch(Exception $e) {
				$result['success'] = 0;
                $result['error'] = Mage::helper('core')->__('Can not save item.');
			}
		}
		
		$action->getResponse()->setHeader('Content-type', 'application/json');
        $action->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
		
		$action->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH,true);
	}
	
	
	/**
	* fired before controller ajax cart delete
	*
	**/
	public function controller_action_predispatch_checkout_cart_ajaxDelete($ob) {
		//Mage::log('222');
		if(!Mage::helper('vendors')->moduleEnabled()) return;
		
		if(Mage::helper('vendors')->getMode() != VES_Vendors_Model_Vendor::MODE_ADVANCED) return;
		
		$id = (int) Mage::app()->getRequest()->getParam('id');
        $result = array();
		$vendorQuoteSession = Mage::getSingleton('vendorscheckout/session');
		$checkoutSession 	= Mage::getSingleton('checkout/session');
		$action = $ob->getControllerAction();
		
		//Mage::log($id);
		
		if($id == null) return;
		if($id) {
			//Mage::log('2');
			try {
				$quoteItem 		= null;
				$vendorQuote 	= null;
				foreach($vendorQuoteSession->getQuotes() as $quote){
					if(!$quote->getVendorId()) continue;
					
					$quoteItem 		= $quote->getItemById($id);
					$vendorQuote 	= $quote;
					if($quoteItem) break;
				}
				//Mage::log('1');
		
				/*Return if the quote item ?not exist on vendor quotes.*/
				if (!$quoteItem) return;
				
			//	Mage::log($quoteItem->getData());
		
				$cart = Mage::getModel('vendorscheckout/cart')->setData('quote',$vendorQuote);
				$cart->removeItem($id)->save();

                $action->loadLayout();
                $result['content'] = $action->getLayout()->getBlock('minicart_content')->toHtml();
				$result['qty'] = $cart->getSummaryQty();
                $result['success'] = 1;
                $result['message'] = Mage::helper('core')->__('Item was removed successfully.');
			}
			catch(Exception $e) {
				$result['success'] = 0;
                $result['error'] = Mage::helper('core')->__('Can not remove the item.');
			}
		}
		
		$action->getResponse()->setHeader('Content-type', 'application/json');
        $action->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
		
		$action->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH,true);
	}
	
	
	public function postdispatch_checkout_onepage_index(Varien_Event_Observer $observer){
	    if(Mage::helper('vendors')->getMode() != VES_Vendors_Model_Vendor::MODE_ADVANCED) return;
	    $action 			= $observer->getControllerAction();
	    $vendorId = $action->getRequest()->getParam('vendor');
	    if(!$vendorId) return;
	    
	    Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getUrl('checkout/onepage/index', array('_secure' => true,'vendor'=>$vendorId)));
	}

	public function loadCustomerQuote()
	{
	
		try {
			if(Mage::helper('vendors')->getMode() != VES_Vendors_Model_Vendor::MODE_ADVANCED){
				Mage::getSingleton('checkout/session')->loadCustomerQuote();
			}
			else{
				Mage::getSingleton('vendorscheckout/session')->loadCustomerQuote();
			}
		}
		catch (Mage_Core_Exception $e) {
			Mage::getSingleton('checkout/session')->addError($e->getMessage());
		}
		catch (Exception $e) {
			Mage::getSingleton('checkout/session')->addException(
				$e,
				Mage::helper('checkout')->__('Load customer quote error')
			);
		}
	}

}