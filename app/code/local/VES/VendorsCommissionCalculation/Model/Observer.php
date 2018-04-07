<?php

class VES_VendorsCommissionCalculation_Model_Observer
{
	/**
	 * Calculate commission
	 * @param Varien_Event_Observer $observer
	 */
	public function ves_vendorscredit_calculate_commission(Varien_Event_Observer $observer){
    	$commissionObj = $observer->getCommission();
    	$invoiceItem   = $observer->getInvoiceItem();
    	$product       = $observer->getProduct();
    	$vendor        = $observer->getVendor();
    	$vendorGroupId = $vendor->getGroupId();
    	$invoice       = $invoiceItem->getInvoice();
    	$storeId       = $invoice->getStoreId();
    	$websiteId     = Mage::app()->getStore($storeId)->getWebsiteId();
    	
    	$ruleCollection = Mage::getModel('vendorscommission/rule')->getCollection()
    	   ->addFieldToFilter('vendor_group_ids',array('finset'=>$vendorGroupId))
    	   ->addFieldToFilter('website_ids',array('finset'=>$websiteId))
    	   ->addFieldToFilter('is_active',VES_VendorsCommissionCalculation_Model_Rule::STATUS_ENABLED)
    	;
    	
    	$today = Mage::getModel('core/date')->date();
    	$ruleCollection->getSelect()
    	   ->where('(from_date IS NULL OR from_date<=?) AND (to_date IS NULL OR to_date>=?)',$today,$today)
    	   ->order('priority ASC');
    	
    	$commission    = 0;

    	if($ruleCollection->count()){
    	    $ruleDescriptionArr = array();
			$fee = 0;//fix undefined var
            foreach($ruleCollection as $rule){
                /*If the product is not match with the conditions just continue*/
                if(!$rule->getConditions()->validate($product)) continue;
                $tmpFee = 0;
                switch($rule->getData('commission_by')){
                    case VES_VendorsCommissionCalculation_Model_Rule::COMMISSION_BY_FIXED_AMOUNT:
                        $tmpFee = $rule->getData('commission_amount');
                        break;
                    case VES_VendorsCommissionCalculation_Model_Rule::COMMISSION_BY_PERCENT_PRODUCT_PRICE:
						if(!$invoiceItem->getData('base_row_total')){
							$baseRowTotal = ($invoiceItem->getData('price_incl_tax') * $invoiceItem->getData('qty')) - $invoiceItem->getData('base_tax_amount');
							$invoiceItem->setData('base_row_total',$baseRowTotal);
						}
                        switch($rule->getData('commission_action')){
                            case VES_VendorsCommissionCalculation_Model_Rule::COMMISSION_BASED_PRICE_INCL_TAX:
                                $amount = $invoiceItem->getData('base_row_total') + $invoiceItem->getData('base_tax_amount');
                                break;
                            case VES_VendorsCommissionCalculation_Model_Rule::COMMISSION_BASED_PRICE_EXCL_TAX:
                                $amount = $invoiceItem->getData('base_row_total');
                                break;
                            case VES_VendorsCommissionCalculation_Model_Rule::COMMISSION_BASED_PRICE_AFTER_DISCOUNT_INCL_TAX:
                                $amount = $invoiceItem->getData('base_row_total') - $invoiceItem->getData('base_discount_amount') + $invoiceItem->getData('base_tax_amount');
                                break;
                            case VES_VendorsCommissionCalculation_Model_Rule::COMMISSION_BASED_PRICE_AFTER_DISCOUNT_EXCL_TAX:
                                $amount = $invoiceItem->getData('base_row_total')  - $invoiceItem->getData('base_discount_amount');
                                break;
                            default:
                                $amount = $invoiceItem->getData('base_row_total')  - $invoiceItem->getData('base_discount_amount');
                        }
                        $tmpFee = ($rule->getData('commission_amount') * $amount)/100;
                        break;
                }
                $tmpFeeWithCurrency = Mage::app()->getLocale()->currency($invoice->getOrder()->getBaseCurrencyCode())->toCurrency($tmpFee,array('precision'=>2));

                $ruleDescriptionArr[] = $rule->getDescription().": -".$tmpFeeWithCurrency;
                
                $fee +=  $tmpFee;
                
                /*Break if the flag stop rules processing is set to 1*/
                if($rule->getData('stop_rules_processing')) break;
            }
            $commissionObj->setFee($fee);
            $commissionObj->setDescriptions($ruleDescriptionArr);
    	}
    	
    }
}