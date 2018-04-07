<?php
class VES_Vendors_Model_Resource_Vendor_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
	protected function _construct()
    {
        $this->_init('vendors/vendor');
    }
    
 	/**
     * Add Name to select
     *
     * @return Mage_Customer_Model_Resource_Customer_Collection
     */
    public function addNameToSelect()
    {
        $fields = array('firstname'=>'firstname','lastname'=>'lastname');
		
        $adapter = $this->getConnection();
        $concatenate = array();
        $concatenate[] = 'LTRIM(RTRIM({{firstname}}))';
        $concatenate[] = '\' \'';
       
        $concatenate[] = 'LTRIM(RTRIM({{lastname}}))';

        $nameExpr = $adapter->getConcatSql($concatenate);

        $this->addExpressionAttributeToSelect('name', $nameExpr, $fields);

        return $this;
    }
}