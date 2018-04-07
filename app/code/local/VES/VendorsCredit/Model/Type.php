<?php
class VES_VendorsCredit_Model_Type extends Varien_Object
{
	protected $_types;
	CONST DEFAULT_TYPE_MODEL	= 'credit/type_model_default';
	/**
	 * Get All avaiable types or individual type
	 * @param string $type
	 * @return array
	 */
	public function getType($type=null){
		if(!$this->_types){
			$this->_types = Mage::getConfig()->getNode('global/credit/types')->asArray();
		}
		return $type?isset($this->_types[$type])? $this->_types[$type]:false:false;
	}
	
	/**
	 * Process transaction data
	 * @param unknown_type $data
	 * @throws Mage_Core_Exception
	 */
	public function process($data = array()){
		$type = isset($data['type'])?$this->getType($data['type']):false;
		if(!$type) throw new Mage_Core_Exception('The transaction type is not exist');
		if(!isset($type['action'])) throw new Mage_Core_Exception('The transaction type does not have action');
		
		$data['action']	= $type['action'];
		$modelClass	= isset($type['class'])?$type['class']:VES_VendorsCredit_Model_Type::DEFAULT_TYPE_MODEL;
		Mage::getModel($modelClass)->setData($data)->process();
	}
	
	/**
	 * Get transaction description
	 * @param VES_VendorsCredit_Model_Transaction $transaction
	 * @throws Mage_Core_Exception
	 */
	public function getDescription(VES_VendorsCredit_Model_Transaction $transaction){
		$type = $this->getType($transaction->getType());
		if(!$type) throw new Mage_Core_Exception('The transaction type is not exist');
		if(!isset($type['action'])) throw new Mage_Core_Exception('The transaction type does not have action');
		
		$modelClass	= isset($type['class'])?$type['class']:VES_VendorsCredit_Model_Type::DEFAULT_TYPE_MODEL;
		return Mage::getModel($modelClass)->getDescription($transaction);
	}
}