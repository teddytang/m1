<?php

$installer = $this;
/**
 * Create table 'ves_vendor_group_rule'
 */

$table = $installer->getConnection()
    ->newTable($installer->getTable('vendorsgroup/rule'))
    ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Rule Id')
   	->addColumn('group_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Group Id')
    ->addColumn('resource_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
        ), 'Resource Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, Varien_Db_Ddl_Table::MAX_TEXT_SIZE, array(
        'nullable'  => false,
        ), 'Rule value');
	$installer->getConnection()->createTable($table);
	
	$installer->getConnection()->addForeignKey('FK_VENDOR_GROUP_RULE', $installer->getTable('vendorsgroup/rule'), 'group_id', $installer->getTable('vendors/group'), 'vendor_group_id');

$installer->endSetup(); 