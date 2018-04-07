<?php

class VES_Vendors_Helper_Data extends Mage_Core_Helper_Abstract
{
	const ROUTE_ACCOUNT_LOGIN 		= 'vendors/index/login';
	const REFERER_QUERY_PARAM_NAME 	= 'referer';
	
	const LOGGED_IN_LAYOUT_HANDLE 	= 'vendors_logged_in';
	const LOGGED_OUT_LAYOUT_HANDLE 	= 'vendors_logged_out';
	const VENDOR_LAYOUT_HANDLE		= 'vendors_default';
	const XML_PATH_CUSTOMER_RESET_PASSWORD_LINK_EXPIRATION_PERIOD
        = 'default/vendors/password/reset_link_expiration_period';
	/**
	 * Module is enabled or not
	 */
	public function moduleEnabled(){
		return Mage::getStoreConfig('vendors/config/active');
	}
	
	public function getVersion(){
		return '1.0.0';
	}
	
	/**
	 * Get extension mode
	 */
	public function getMode(){
		return Mage::getStoreConfig('vendors/config/mode');
	}
	/**
	 * Is advanced mode
	 */
	public function isAdvancedMode(){
		return in_array($this->getMode(),array(VES_Vendors_Model_Vendor::MODE_ADVANCED,VES_Vendors_Model_Vendor::MODE_ADVANCED_X));
	}
	
	/**
	 * Display vendor login url on toplink?
	 */
	public function displayVendorUrlOnTopLink(){
		$result = new Varien_Object(array('result'=>true));
		Mage::dispatchEvent('ves_vendors_display_vendor_top_link',array('result'=>$result));
		return $result->getResult();
	}
	
	public function getDefaultConfig(){
		
	}
	
	public function getStoreManageUrl(){
		return Mage::getUrl('vendors');
	}
	/**
     * Retrieve customer login url
     *
     * @return string
     */
    public function getLoginUrl()
    {
        return $this->_getUrl(self::ROUTE_ACCOUNT_LOGIN, $this->getLoginUrlParams());
    }
	/**
     * Retrieve vendor login POST URL
     *
     * @return string
     */
    public function getLoginPostUrl()
    {
        $params = array();
        if ($this->_getRequest()->getParam(self::REFERER_QUERY_PARAM_NAME)) {
            $params = array(
                self::REFERER_QUERY_PARAM_NAME => $this->_getRequest()->getParam(self::REFERER_QUERY_PARAM_NAME)
            );
        }
        return $this->_getUrl('vendors/index/loginPost', $params);
    }
	/**
     * Retrieve vendor logout url
     *
     * @return string
     */
    public function getLogoutUrl()
    {
        return $this->_getUrl('vendors/index/logout');
    }
	/**
     * Retrieve vendors dashboard url
     *
     * @return string
     */
    public function getDashboardUrl()
    {
        return $this->_getUrl('vendors/dashboard');
    }
	/**
     * Retrieve vendor register form url
     *
     * @return string
     */
    public function getRegisterUrl()
    {
        return $this->_getUrl('vendors/index/create');
    }

    /**
     * Retrieve vendor register form post url
     *
     * @return string
     */
    public function getRegisterPostUrl()
    {
        return $this->_getUrl('vendors/index/createpost');
    }
	/**
     * Retrieve vendor account edit form url
     *
     * @return string
     */
    public function getEditUrl()
    {
        return $this->_getUrl('vendors/account/edit');
    }

    /**
     * Retrieve vendor edit POST URL
     *
     * @return string
     */
    public function getEditPostUrl()
    {
        return $this->_getUrl('vendors/account/editpost');
    }

    /**
     * Retrieve url of forgot password page
     *
     * @return string
     */
    public function getForgotPasswordUrl()
    {
        return $this->_getUrl('vendors/account/forgotpassword');
    }
    /**
     * Retrieve confirmation URL for Email
     *
     * @param string $email
     * @return string
     */
    public function getEmailConfirmationUrl($email = null)
    {
        return $this->_getUrl('vendors/index/confirmation', array('email' => $email));
    }
	/**
     * Retrieve vendor account page url
     *
     * @return string
     */
    public function getAccountUrl()
    {
        return $this->_getUrl('vendors/account');
    }
    
    public function getSettingUrl(){
   		return $this->_getUrl('vendors/setting');
    }
	/**
     * Retrieve parameters of vendor login url
     *
     * @return array
     */
    public function getLoginUrlParams()
    {
        $params = array();

        $referer = $this->_getRequest()->getParam(self::REFERER_QUERY_PARAM_NAME);


        if ($referer) {
            $params = array(self::REFERER_QUERY_PARAM_NAME => $referer);
        }

        return $params;
    }
    
    /**
     * Get all option value by attribute
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     * @param boolean $blankValue
     * 
     * @return array
     */
    public function getOptionsByAttribute(Mage_Eav_Model_Entity_Attribute $attribute, $blankValue = false){
    	$storeId = Mage::app()->getStore()->getId();
	    $collection = Mage::getResourceModel('eav/entity_attribute_option_collection')
								->setPositionOrder('asc')
								->setAttributeFilter($attribute->getAttributeId())
								->setStoreFilter($storeId);
		$result = array();
		if($blankValue){
			$result[] = array(
				'label'	=> $this->__('Select an option'),
				'value'	=> '',
			);
		}
		foreach($collection as $option) {
			$result[] = array(
				'label'	=> $option->getValue(),
				'value'	=> $option->getId(),
			);
		}
		return $result;
    }
    
	/**
     * Get html input of an attribute
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     */
    public function getAttributeInputHtml(Mage_Eav_Model_Entity_Attribute $attribute){
    	$inputData = array(
    		'id' 		=> $attribute->getAttributeCode(),
    		'name'		=> $attribute->getAttributeCode(),
    		'title'		=> $attribute->getStoreLabel(),
    		'label'		=> $attribute->getStoreLabel(),
    		'class' 	=> $attribute->getFrontendClass(),
    		'required' 	=> $attribute->getIsRequired(),
    	);
    	switch ($attribute->getFrontendInput()){
    		case "select":
    			$inputData['values'] = $this->getOptionsByAttribute($attribute,true);
    			$field = new VES_Vendors_Block_Form_Element_Select($inputData);
    			break;
    		case 'multiselect':
    			$inputData['values'] = $this->getOptionsByAttribute($attribute);
    			$field = new VES_Vendors_Block_Form_Element_Multiselect($inputData);
    			break;
    		case 'boolean':
    			$field = new VES_Vendors_Block_Form_Element_Yesno($inputData);
    			break;
    		case 'textarea':
    			$field = new VES_Vendors_Block_Form_Element_Textarea($inputData);
    			break;
    		case 'date':
    			$inputData['format']	= Varien_Date::DATE_INTERNAL_FORMAT;
    			$inputData['image']		= Mage::getDesign()->getSkinUrl('ves_vendors/images/grid-cal.gif');
    			$field = new VES_Vendors_Block_Form_Element_Date($inputData);
    			break;
    		case "text":
    			$field = new VES_Vendors_Block_Form_Element_Text($inputData);
    			break;
			case "file":
			    $field = new VES_Vendors_Block_Form_Element_File($inputData);
			    break;
    		default:
    			$field = new VES_Vendors_Block_Form_Element_Text($inputData);
    	}
    	return $field;
    }
	/**
     * Generate unique token for reset password confirmation link
     *
     * @return string
     */
    public function generateResetPasswordLinkToken()
    {
        return Mage::helper('core')->uniqHash();
    }
    
	/**
     * Retrieve customer reset password link expiration period in days
     *
     * @return int
     */
    public function getResetPasswordLinkExpirationPeriod()
    {
        return (int) Mage::getConfig()->getNode(self::XML_PATH_CUSTOMER_RESET_PASSWORD_LINK_EXPIRATION_PERIOD);
    }
    
    public function approvalRequired(){
    	return Mage::getStoreConfig('vendors/create_account/approval');
    }
    
	/**
	 * Get total amount for fee calculation
	 * @param Object $invoice
	 */
	public function getTotalAmountForFeeCalculation($obj){
		if($obj instanceof Mage_Sales_Model_Order_Invoice || $obj instanceof Mage_Sales_Model_Order){
			switch(Mage::getStoreConfig('vendors/config/calculation_algorithm')){
				case VES_Vendors_Model_Source_Calculation::GRANDTOTAL:
					return $obj->getBaseGrandTotal();
				case VES_Vendors_Model_Source_Calculation::SUBTOTAL:
					return $obj->getBaseSubtotal();
				case VES_Vendors_Model_Source_Calculation::SUBTOTAL_AFTER_DISCOUNT:
					return $obj->getBaseSubtotal()-$obj->getBaseDiscountAmount();
			}
		}elseif($obj instanceof Mage_Sales_Model_Order_Invoice_Item){
			switch(Mage::getStoreConfig('vendors/config/calculation_algorithm')){
				case VES_Vendors_Model_Source_Calculation::ITEM_ROW_TOTAL:
					return $obj->getBaseRowTotal();
			}
		}
		return 0;
	}
	
	public function getAttributeInfo($attributeCode){
		return Mage::getModel('eav/entity_attribute')->loadByCode('ves_vendor', $attributeCode);
	}

	public function getIsRequired($attributeCode){
		return $this->getAttributeInfo($attributeCode)->getIsRequired();
	}
}