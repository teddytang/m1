<?php

$installer = $this;

$catalogEavSetup = new Mage_Catalog_Model_Resource_Eav_Mysql4_Setup('core_setup');
$catalogEavSetup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'vendor_sku', array(
	'group' 						=> 'General',
	'sort_order' 					=> 20,
	'type' 							=> 'static',
	'backend' 						=> '',
	'frontend' 						=> '',
	'label' 						=> 'SKU',
	'note' 							=> '',
	'input' 						=> 'text',
	'class' 						=> '',
	'source' 						=> '',
	'global' 						=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
	'visible' 						=> true,
	'required' 						=> true,
	'user_defined' 					=> false,
	'default' 						=> '',
	'visible_on_front' 				=> false,
	'unique' 						=> false,
	'is_configurable' 				=> false,
	'used_for_promo_rules' 			=> false,
	'searchable'        			=> true,
	'filterable'        			=> false,
	'comparable'        			=> true,
	'visible_in_advanced_search' 	=> true,
));


$this->getConnection()->addColumn($this->getTable('catalog/product'), 'vendor_sku', 'text DEFAULT NULL AFTER sku');

$installer->endSetup(); 