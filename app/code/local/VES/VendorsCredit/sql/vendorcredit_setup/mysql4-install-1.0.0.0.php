<?php

$installer = $this;
/**
 * Create table 'vendor_transaction'
 */

$table = $installer->getConnection()
    ->newTable($installer->getTable('vendorscredit/transaction'))
    ->addColumn('transaction_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Transaction Id')
    ->addColumn('vendor_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Vendor Id')
    ->addColumn('type', Varien_Db_Ddl_Table::TYPE_VARCHAR, 32, array(
        'nullable'  => false,
        ), 'Transaction Type')
    ->addColumn('amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
        'nullable'  => false,
        ), 'Transaction amount')
    ->addColumn('fee', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
        'nullable'  => false,
        ), 'Fee')
    ->addColumn('net_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
        'nullable'  => false,
        ), 'Net amount')
    ->addColumn('balance', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
        'nullable'  => false,
        ), 'Balance amount')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, Varien_Db_Ddl_Table::MAX_TEXT_SIZE, array(
        'nullable'  => true,
        ), 'Transaction description')
    ->addColumn('additional_info', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
        ), 'Additional info')
   	->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable'  => false,
        ), 'Created At')
        ;
$installer->getConnection()->createTable($table);


$table = $installer->getConnection()
    ->newTable($installer->getTable('vendorscredit/payment'))
    ->addColumn('method_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Payment method Id')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
        ), 'Payment method name')
    ->addColumn('fee', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
        'nullable'  => false,
        ), 'Withdrawal Fee')
    ->addColumn('min', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
        'nullable'  => false,
        ), 'Minimum amount')
   	->addColumn('max', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
        'nullable'  => false,
        ), 'Maximum amount')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, Varien_Db_Ddl_Table::MAX_TEXT_SIZE, array(
        'nullable'  => true,
        ), 'Payment description')
    ->addColumn('note', Varien_Db_Ddl_Table::TYPE_TEXT, Varien_Db_Ddl_Table::MAX_TEXT_SIZE, array(
        'nullable'  => true,
        ), 'Note message')
    ->addColumn('additional_info', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
        ), 'Additional info')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array(
        'nullable'  => false,
        ), 'Sort Order')
        ;
$installer->getConnection()->createTable($table);
$installer->getConnection()->insertForce($installer->getTable('vendorscredit/payment'), array(
    'method_id'     => 1,
    'name'   		=> 'Paypal',
	'fee'   		=> 0,
	'description'   => 'Withdraw funds to your PayPal account.',
	'additional_info'	=> 'a:2:{s:19:"allow_email_account";s:1:"1";s:25:"allow_additional_textarea";s:1:"0";}',
	'sort_order'	=> 0,
));
$installer->getConnection()->insertForce($installer->getTable('vendorscredit/payment'), array(
    'method_id'     => 2,
    'name'   		=> 'Wire Transfer',
	'fee'   		=> 0,
	'description'   => 'Withdraw funds directly to your back account. For countries where Express Withdrawal is unavailable.',
	'note'			=> '<strong> Important! </strong>Please fill in all relevant details including:<br /><ul style="list-style: disc inside none;"><li>Your Full Name, Address, Country, Zip/Post Code</li><li>Bank Account Number, Bank Name, Bank Code, Address of Bank</li></ul>',
	'additional_info'	=> 'a:2:{s:19:"allow_email_account";s:1:"0";s:25:"allow_additional_textarea";s:1:"1";}',
	'sort_order'	=> 1,
));

$table = $installer->getConnection()
    ->newTable($installer->getTable('vendorscredit/withdrawal'))
    ->addColumn('withdrawal_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Withdrawal Id')
    ->addColumn('vendor_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'vendor Id')
    ->addColumn('method', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
        ), 'Payment method name')
    ->addColumn('amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
        'nullable'  => false,
        ), 'Amount')
    ->addColumn('fee', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
        'nullable'  => false,
        ), 'Withdrawal Fee')
   ->addColumn('net_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
        'nullable'  => false,
        ), 'Net Amount')
    ->addColumn('additional_info', Varien_Db_Ddl_Table::TYPE_TEXT, Varien_Db_Ddl_Table::MAX_TEXT_SIZE, array(
        'nullable'  => false,
        ), 'Additional info')
    ->addColumn('note', Varien_Db_Ddl_Table::TYPE_TEXT, Varien_Db_Ddl_Table::MAX_TEXT_SIZE, array(
        'nullable'  => false,
        ), 'Note from admin')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_TINYINT, 255, array(
        'nullable'  => false,
        ), 'Status')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_DATETIME, 255, array(
        'nullable'  => false,
        ), 'Created at')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_DATETIME, 255, array(
        'nullable'  => false,
        ), 'Updated at')
        ;
$installer->getConnection()->createTable($table);
/* Add credit amount for vendor */

$this->addAttribute('ves_vendor', 'credit', array(
        'type'              => 'static',
        'label'             => 'Credit Amount',
        'input'             => 'text',
        'class'             => '',
        'backend'           => '',
        'frontend'          => '',
        'source'            => '',
        'required'          => true,
        'user_defined'      => false,
        'default'           => 0,
        'unique'            => false,
));
$this->getConnection()->addColumn($this->getTable('vendors/vendor'), 'credit', 'varchar(255) NOT NULL DEFAULT 0 AFTER vendor_id');
$installer->endSetup(); 