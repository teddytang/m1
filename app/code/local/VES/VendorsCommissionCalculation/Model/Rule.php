<?php
/**
 * Rule
 *
 * @category   VES
 * @package    VES_VBlock
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsCommissionCalculation_Model_Rule extends Mage_CatalogRule_Model_Rule
{
    const STATUS_ENABLED    = 1;
    const STATUS_DISABLED   = 0;
    
    const COMMISSION_BY_FIXED_AMOUNT            = 'by_fixed';
    const COMMISSION_BY_PERCENT_PRODUCT_PRICE   = 'by_percent';
    
    const COMMISSION_BASED_PRICE_INCL_TAX       = 'by_price_incl_tax';
    const COMMISSION_BASED_PRICE_EXCL_TAX       = 'by_price_excl_tax';
    const COMMISSION_BASED_PRICE_AFTER_DISCOUNT_INCL_TAX       = 'by_price_after_discount_incl_tax';
    const COMMISSION_BASED_PRICE_AFTER_DISCOUNT_EXCL_TAX       = 'by_price_after_discount_excl_tax';
    
    /**
     * Init resource model and id field
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('vendorscommission/rule');
        $this->setIdFieldName('rule_id');
    }
	/**
     * Getter for rule conditions collection
     *
     * @return Mage_CatalogRule_Model_Rule_Condition_Combine
     */
    public function getConditionsInstance()
    {
        return Mage::getModel('vendorscommission/rule_condition_combine');
    }
    
    protected function _beforeSave(){
        parent::_beforeSave();
        if($this->getData('commission_by') == self::COMMISSION_BY_FIXED_AMOUNT){
			$this->setData('commission_action','');
		}
        if(is_array($this->getWebsiteIds())){
            $this->setWebsiteIds(implode(",", $this->getWebsiteIds()));
        }
    }
}