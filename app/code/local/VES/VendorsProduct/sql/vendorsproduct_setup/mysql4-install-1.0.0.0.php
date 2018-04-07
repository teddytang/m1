<?php

$installer = $this;

$catalogEavSetup = new Mage_Catalog_Model_Resource_Eav_Mysql4_Setup('core_setup');

$catalogEavSetup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'vendor_id', array(
	'group' => 'General',
	'sort_order' => 20,
	'type' => 'static',
	'backend' => '',
	'frontend' => '',
	'label' => 'Vendor Id',
	'note' => 'Vendor Id',
	'input' => 'text',
	'class' => '',
	'source' => '',
	'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
	'visible' => true,
	'required' => false,
	'user_defined' => false,
	'default' => '',
	'visible_on_front' => false,
	'unique' => false,
	'is_configurable' => false,
	'used_for_promo_rules' => true,
	'used_in_product_listing'  => true,
));

$catalogEavSetup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'approval', array(
	'group' => 'General',
	'sort_order' => 21,
	'type' => 'int',
	'backend' => '',
	'frontend' => '',
	'label' => 'Approval',
	'note' => '',
	'input' => 'select',
	'class' => '',
	'source' => 'vendorsproduct/source_approval',
	'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
	'visible' => true,
	'required' => false,
	'user_defined' => false,
	'default' => '1',
	'visible_on_front' => false,
	'unique' => false,
	'is_configurable' => false,
	'used_for_promo_rules' => false,
	'used_in_product_listing'  => true,
));

$this->getConnection()->addColumn($this->getTable('catalog/product'), 'vendor_id', 'int(10)  UNSIGNED NOT NULL AFTER type_id');

$installer->endSetup(); 