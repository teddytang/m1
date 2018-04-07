<?php

class VES_VendorsGroup_Model_Resource_Rule extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
    {    
        $this->_init('vendorsgroup/rule', 'rule_id');
    }
    
    public function deleteRulesByGroup($groupId){
    	if($groupId instanceof VES_Vendors_Model_Group){
    		$groupId = $groupId->getId();
    	}
    	
    	$adapter = $this->_getWriteAdapter();
    	$adapter->query("DELETE FROM {$this->getMainTable()} WHERE group_id='{$groupId}'");
    }
    
    public function getConfig($resourceId, $groupId){
    	$adapter = $this->_getReadAdapter();
    	$select = $adapter->select()
            ->from($this->getMainTable(), 'value')
            ->where('resource_id = :resource_id')
            ->where('group_id = :group_id');
        $bind = array('resource_id'=>$resourceId, 'group_id'=>$groupId);
        $result = $adapter->fetchOne($select, $bind);
        return $result;
    }
}