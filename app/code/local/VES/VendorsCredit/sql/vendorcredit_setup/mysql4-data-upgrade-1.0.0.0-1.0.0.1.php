<?php 
$installer = $this;
/*
$table = $installer->getConnection()
->newTable($installer->getTable('vendorscredit/commission_rule'))
->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'identity'  => true,
    'unsigned'  => true,
    'nullable'  => false,
    'primary'   => true,
), 'Commisison Rule Id')
->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 32, array(
    'nullable'  => false,
), 'Rule Name')
->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
    'nullable'  => false,
), 'Rule Description')
->addColumn('from_date', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
    'nullable'  => false,
), 'From Date')
->addColumn('to_date', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
    'nullable'  => false,
), 'To Date')
->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
    'nullable'  => false,
), 'Is Active')
->addColumn('conditions_serialized', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
    'nullable'  => false,
), 'Conditions Serialized')
->addColumn('stop_rules_processing', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
    'nullable'  => false,
), 'Stop rules processing')
->addColumn('priority', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'nullable'  => false,
), 'Rule Priority')

->addColumn('commission_action', Varien_Db_Ddl_Table::TYPE_VARCHAR, 32, array(
    'nullable'  => false,
), 'Commision Type')
->addColumn('commission_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
    'nullable'  => false,
), 'Commision amount')
;
$installer->getConnection()->createTable($table);
*/

$table = $installer->getConnection()
    ->newTable($installer->getTable('vendorscredit/escrow'))
    ->addColumn('escrow_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Escrow Id')
    ->addColumn('vendor_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Vendor Id')
    ->addColumn('relation_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Invoice Id or Invoice item id (depends on extension mode)')
    ->addColumn('amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
            'nullable'  => false,
    ), 'Escrow amount')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'nullable'  => false,
    ), 'Escrow status')
    ->addColumn('additional_info', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
    ), 'Additional info')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable'  => false,
    ), 'Created At')
;

$installer->getConnection()->createTable($table);
$installer->endSetup();