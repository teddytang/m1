<?php

$installer = $this;
/**
 * Create table 'vendor_message'
 */

$table = $installer->getConnection()
    ->newTable($installer->getTable('vendorsconfig/config'))
    ->addColumn('config_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Config Id')
   	->addColumn('vendor_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Vendor Id')
    ->addColumn('path', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
        ), 'Path')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, Varien_Db_Ddl_Table::MAX_TEXT_SIZE, array(
        'nullable'  => false,
        ), 'Config value');
	$installer->getConnection()->createTable($table);
	
	$installer->getConnection()->addForeignKey('FK_VENDOR', $installer->getTable('vendorsconfig/config'), 'vendor_id', $installer->getTable('vendors/vendor'), 'entity_id');

$installer->endSetup(); 