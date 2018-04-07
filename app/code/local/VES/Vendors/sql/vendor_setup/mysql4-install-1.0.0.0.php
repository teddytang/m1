<?php

$installer = $this;
/**
 * Create table 'vendor_group'
 */

$table = $installer->getConnection()
    ->newTable($installer->getTable('vendors/group'))
    ->addColumn('vendor_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Vendor Group Id')
    ->addColumn('vendor_group_code', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        'nullable'  => false,
        ), 'Vendor Group Code')
    ->addColumn('fee', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
        'nullable'  => false,
        ), 'Fee per order')
    ->addColumn('fee_by', Varien_Db_Ddl_Table::TYPE_TINYINT, 6, array(
        'nullable'  => false,
        ), 'Calculate Fee By')
    ->setComment('Customer Group');
$installer->getConnection()->createTable($table);

$installer->getConnection()->insertForce($installer->getTable('vendors/group'), array(
    'vendor_group_id'     => 1,
    'vendor_group_code'   => 'General',
));

$installer->addEntityType('ves_vendor', array(
    'entity_model'    => 'vendors/vendor',
    'table'           =>'vendors/vendor',
));

$installer->createEntityTables(
    'vendors/vendor'
);
$this->addAttribute('ves_vendor', 'firstname', array(
        'type'              => 'varchar',
        'label'             => 'First Name',
        'input'             => 'text',
        'class'             => '',
        'backend'           => '',
        'frontend'          => '',
        'source'            => '',
        'required'          => true,
        'user_defined'      => false,
        'default'           => '',
        'unique'            => false,
));

$this->addAttribute('ves_vendor', 'lastname', array(
        'type'              => 'varchar',
        'label'             => 'Last Name',
        'input'             => 'text',
        'class'             => '',
        'backend'           => '',
        'frontend'          => '',
        'source'            => '',
        'required'          => true,
        'user_defined'      => false,
        'default'           => '',
        'unique'            => false,
));
$this->addAttribute('ves_vendor', 'vendor_id', array(
        'type'              => 'static',
        'label'             => 'Vendor Id',
        'input'             => 'text',
        'class'             => '',
        'backend'           => '',
        'frontend'          => '',
        'source'            => '',
        'required'          => true,
        'user_defined'      => false,
        'default'           => '',
        'unique'            => true,
));
$this->addAttribute('ves_vendor', 'group_id', array(
        'type'              => 'static',
        'label'             => 'Group',
        'input'             => 'select',
        'class'             => '',
        'backend'           => '',
        'frontend'          => '',
        'source'        	=> 'vendors/source_group',
        'required'          => true,
        'user_defined'      => false,
        'default'           => '',
        'unique'            => false,
));
$this->addAttribute('ves_vendor', 'confirmation', array(
        'type'              => 'varchar',
        'label'             => 'Is Confirmed',
        'input'             => 'text',
        'class'             => '',
        'backend'           => '',
        'frontend'          => '',
        'source'            => '',
        'required'          => false,
        'user_defined'      => false,
        'default'           => '',
        'unique'            => false,
));
$this->addAttribute('ves_vendor', 'title', array(
        'type'              => 'varchar',
        'label'             => 'Title',
        'input'             => 'text',
        'class'             => '',
        'backend'           => '',
        'frontend'          => '',
        'source'            => '',
        'required'          => true,
        'user_defined'      => false,
        'default'           => '',
        'unique'            => false,
));
$this->addAttribute('ves_vendor', 'company', array(
        'type'              => 'varchar',
        'label'             => 'Company',
        'input'             => 'text',
        'class'             => '',
        'backend'           => '',
        'frontend'          => '',
        'source'            => '',
        'required'          => false,
        'user_defined'      => false,
        'default'           => '',
        'unique'            => false,
));
$this->addAttribute('ves_vendor', 'email', array(
        'type'              => 'static',
        'label'             => 'Email',
        'input'             => 'text',
        'class'             => '',
        'backend'           => '',
        'frontend'          => '',
        'source'            => '',
        'required'          => true,
        'user_defined'      => false,
        'default'           => '',
        'unique'            => true,
));
$this->addAttribute('ves_vendor', 'logo', array(
        'type'              => 'varchar',
        'label'             => 'Logo',
        'input'             => 'text',
        'class'             => '',
        'backend'           => '',
        'frontend_input'  	=> 'image',
        'frontend'          => '',
        'source'            => '',
        'required'          => false,
        'user_defined'      => false,
        'default'           => '',
        'unique'            => false,
));
$this->addAttribute('ves_vendor', 'address', array(
        'type'              => 'varchar',
        'label'             => 'Address',
        'input'             => 'text',
        'class'             => '',
        'backend'           => '',
        'frontend'          => '',
        'source'            => '',
        'required'          => true,
        'user_defined'      => false,
        'default'           => '',
        'unique'            => false,
));

$this->addAttribute('ves_vendor', 'city', array(
        'type'              => 'varchar',
        'label'             => 'City',
        'input'             => 'text',
        'class'             => '',
        'backend'           => '',
        'frontend'          => '',
        'source'            => '',
        'required'          => true,
        'user_defined'      => false,
        'default'           => '',
        'unique'            => false,
));

$this->addAttribute('ves_vendor', 'region', array(
        'type'              => 'varchar',
        'label'             => 'State/Province',
        'input'             => 'text',
        'class'             => '',
        'backend'           => '',
        'frontend'          => '',
        'source'            => '',
        'required'          => true,
        'user_defined'      => false,
        'default'           => '',
        'unique'            => false,
));
$this->addAttribute('ves_vendor', 'region_id', array(
        'type'              => 'int',
        'label'             => 'State/Province',
        'input'             => 'hidden',
        'class'             => '',
        'backend'           => '',
        'frontend'          => '',
        'source'            => '',
        'required'          => true,
        'user_defined'      => false,
        'default'           => '',
        'unique'            => false,
));

$this->addAttribute('ves_vendor', 'country_id', array(
        'type'              => 'varchar',
        'label'             => 'Country',
        'input'             => 'text',
        'class'             => '',
        'backend'           => '',
        'frontend'          => '',
        'source'            => '',
        'required'          => true,
        'user_defined'      => false,
        'default'           => '',
        'unique'            => false,
));

$this->addAttribute('ves_vendor', 'postcode', array(
        'type'              => 'varchar',
        'label'             => 'Zip/Postal Code',
        'input'             => 'text',
        'class'             => '',
        'backend'           => '',
        'frontend'          => '',
        'source'            => '',
        'required'          => true,
        'user_defined'      => false,
        'default'           => '',
        'unique'            => false,
));
$this->addAttribute('ves_vendor', 'telephone', array(
        'type'              => 'varchar',
        'label'             => 'Telephone',
        'input'             => 'text',
        'class'             => '',
        'backend'           => '',
        'frontend'          => '',
        'source'            => '',
        'required'          => false,
        'user_defined'      => false,
        'default'           => '',
        'unique'            => false,
));
$this->addAttribute('ves_vendor', 'fax', array(
        'type'              => 'varchar',
        'label'             => 'Fax',
        'input'             => 'text',
        'class'             => '',
        'backend'           => '',
        'frontend'          => '',
        'source'            => '',
        'required'          => false,
        'user_defined'      => false,
        'default'           => '',
        'unique'            => false,
));

$this->addAttribute('ves_vendor', 'password_hash', array(
        'type'              => 'varchar',
        'input'             => 'hidden',
		'frontend_input'	=> 'hidden',
        'class'             => '',
		'backend'     		=> 'vendors/vendor_attribute_backend_password',
        'frontend'          => '',
        'required'          => false,
        'user_defined'      => false,
        'unique'            => false,
));

$this->addAttribute('ves_vendor', 'facebook', array(
        'type'              => 'varchar',
        'label'             => 'Facebook Id',
        'input'             => 'text',
        'class'             => '',
        'backend'           => '',
        'frontend'          => '',
        'source'            => '',
        'required'          => false,
        'user_defined'      => true,
        'default'           => '',
        'unique'            => false,
));
$this->addAttribute('ves_vendor', 'twitter', array(
        'type'              => 'varchar',
        'label'             => 'Twitter Id',
        'input'             => 'text',
        'class'             => '',
        'backend'           => '',
        'frontend'          => '',
        'source'            => '',
        'required'          => false,
        'user_defined'      => true,
        'default'           => '',
        'unique'            => false,
));

$this->addAttribute('ves_vendor', 'status', array(
        'type'              => 'static',
        'label'             => 'Status',
        'input'             => 'select',
        'class'             => '',
        'backend'           => '',
        'frontend'          => '',
        'source'        	=> 'vendors/source_status',
        'required'          => true,
        'user_defined'      => false,
        'default'           => '',
        'unique'            => false,
));
$this->addAttribute('ves_vendor', 'created_at', array(
        'type'              => 'static',
        'label'             => 'Created At',
        'input'             => 'date',
		'frontend_input'	=> 'date',
        'class'             => '',
        'backend'           => '',
        'frontend'          => '',
        'required'          => false,
        'user_defined'      => false,
        'default'           => '',
        'unique'            => false,
));

$this->addAttribute('ves_vendor', 'website_id', array(
        'type'              => 'static',
        'label'             => 'Website',
        'input'             => 'select',
		'frontend_input'	=> 'select',
        'class'             => '',
        'backend'     		=> 'vendors/vendor_attribute_backend_website',
		'source'			=> 'vendors/vendor_attribute_source_website',
        'frontend'          => '',
        'required'          => true,
        'user_defined'      => false,
        'default'           => '',
        'unique'            => false,
));
$this->addAttribute('ves_vendor', 'store_id', array(
        'type'              => 'static',
        'label'             => 'Store',
        'input'             => 'select',
		'frontend_input'	=> 'select',
        'class'             => '',
		'backend'     		=> 'vendors/vendor_attribute_backend_store',
		'source'			=> 'vendors/vendor_attribute_source_store',
        'frontend'          => '',
        'required'          => true,
        'user_defined'      => false,
        'default'           => '',
        'unique'            => false,
));

$this->getConnection()->addColumn($this->getTable('vendors/vendor'), 'website_id', 'smallint(5) unsigned DEFAULT NULL AFTER attribute_set_id');
$this->getConnection()->addColumn($this->getTable('vendors/vendor'), 'email', 'varchar(255) DEFAULT NULL AFTER website_id');
$this->getConnection()->addColumn($this->getTable('vendors/vendor'), 'vendor_id', 'varchar(255) DEFAULT NULL AFTER email');
$this->getConnection()->addColumn($this->getTable('vendors/vendor'), 'group_id', 'smallint(5) unsigned NOT NULL DEFAULT \'0\' AFTER website_id');
$this->getConnection()->addColumn($this->getTable('vendors/vendor'), 'status', 'smallint(5) unsigned NOT NULL AFTER updated_at');

$this->addAttribute('ves_vendor', 'rp_token', array(
        'type'              => 'varchar',
        'label'             => null,
        'input'             => 'hidden',
		'frontend_input'	=> 'hidden',
        'class'             => null,
		'backend'     		=> null,
		'source'			=> null,
        'frontend'          => null,
        'required'          => false,
        'user_defined'      => false,
        'default'           => null,
        'unique'            => false,
));
$this->addAttribute('ves_vendor', 'rp_token_created_at', array(
        'type'              => 'datetime',
        'label'             => null,
        'input'             => 'date',
		'frontend_input'	=> 'date',
        'class'             => null,
		'backend'     		=> null,
		'source'			=> null,
        'frontend'          => null,
        'required'          => false,
        'user_defined'      => false,
        'default'           => null,
        'unique'            => false,
));

$installer->endSetup(); 