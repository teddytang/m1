<?php

$installer = $this;
/**
 * Create table 'vendor_message'
 */
$msgBoxTable = $installer->getConnection()
    ->newTable($installer->getTable('vendorsmessage/msgbox'))
    ->addColumn('msgbox_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Message Box Id')
    ->addColumn('owner_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Owner Id')
    ->addColumn('email', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
        ), 'Owner Email')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
        ), 'Owner Name')
	->addColumn('type', Varien_Db_Ddl_Table::TYPE_VARCHAR, 32, array(
        'nullable'  => false,
        ), 'Owner Account Type: "vendor","admin","customer"')
        ;
$installer->getConnection()->createTable($msgBoxTable);

$msgTable = $installer->getConnection()
    ->newTable($installer->getTable('vendorsmessage/message'))
    ->addColumn('message_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Message Id')
    ->addColumn('msgbox_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Owner Id')
    ->addColumn('parent_message_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Parent Message Id')
    ->addColumn('from_msgbox_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'From Msgbox Id')
    ->addColumn('from', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
        ), 'From Account Email')
    ->addColumn('to_msgbox_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'To Msgbox Id')
    ->addColumn('to', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
        ), 'To Account Email')
	->addColumn('subject', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
        ), 'Message Subject')
    ->addColumn('content', Varien_Db_Ddl_Table::TYPE_TEXT, Varien_Db_Ddl_Table::MAX_TEXT_SIZE, array(
        'nullable'  => false,
        ), 'Message Content')
    ->addColumn('state', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        ), 'Message state')
   	->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable'  => false,
        ), 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable'  => false,
        ), 'Updated At')
        ->addForeignKey('FK_VENDOR_MESSAGE_BOX',
                    'msgbox_id', $installer->getTable('vendorsmessage/msgbox'), 'msgbox_id',
                    Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ;
$installer->getConnection()->createTable($msgTable);

$installer->endSetup(); 