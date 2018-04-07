<?php
/**
 * Vendor register form block
 *
 * @author      Vnecoms Team <support@vnecoms.com>
 */
class VES_Vendors_Block_Form_Register extends Mage_Directory_Block_Data
{

    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    /**
     * Retrieve form posting url
     *
     * @return string
     */
    public function getPostActionUrl()
    {
        return $this->helper('vendors')->getRegisterPostUrl();
    }
	public function getLoginUrl(){
		return $this->helper('vendors')->getLoginUrl();
	}
    /**
     * Retrieve back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        $url = $this->getData('back_url');
        if (is_null($url)) {
            $url = $this->helper('vendors')->getLoginUrl();
        }
        return $url;
    }

    /**
     * Retrieve form data
     *
     * @return Varien_Object
     */
    public function getFormData()
    {
        $data = $this->getData('form_data');
        if (is_null($data)) {
            $formData = Mage::getSingleton('vendors/session')->getVendorFormData(true);
            $data = new Varien_Object();
            if ($formData) {
                $data->addData($formData);
                $data->setCustomerData(1);
            }
            if (isset($data['region_id'])) {
                $data['region_id'] = (int)$data['region_id'];
            }
            $this->setData('form_data', $data);
        }
        return $data;
    }

    /**
     * Retrieve customer country identifier
     *
     * @return int
     */
    public function getCountryId()
    {
        $countryId = $this->getFormData()->getCountryId();
        if ($countryId) {
            return $countryId;
        }
        return parent::getCountryId();
    }

    /**
     * Retrieve customer region identifier
     *
     * @return int
     */
    public function getRegion()
    {
        if (false !== ($region = $this->getFormData()->getRegion())) {
            return $region;
        } else if (false !== ($region = $this->getFormData()->getRegionId())) {
            return $region;
        }
        return null;
    }
    /**
     * Get All additional attributes
     * 
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Collection
     */
    public function getAdditionalAttributes(){
    	$vendorAttributeType = Mage::getResourceModel('eav/entity_type_collection')->addFieldToFilter('entity_type_code','ves_vendor')->getFirstItem();
    	$collection = Mage::getResourceModel('eav/entity_attribute_collection')
	      ->addFieldToFilter('entity_type_id',$vendorAttributeType->getId())
	      ->addFieldToFilter('is_user_defined',true);
	    return $collection;
    }
    /**
     * Get html input of an attribute
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     */
    public function getAttributeInputHtml(Mage_Eav_Model_Entity_Attribute $attribute){
    	return Mage::helper('vendors')->getAttributeInputHtml($attribute);
    }
	
	public function getAttributeInfo($attributeCode){
        return Mage::getModel('eav/entity_attribute')->loadByCode('ves_vendor', $attributeCode);
    }
    
    public function getIsRequired($attributeCode){
        return $this->getAttributeInfo($attributeCode)->getIsRequired();
    }
}
