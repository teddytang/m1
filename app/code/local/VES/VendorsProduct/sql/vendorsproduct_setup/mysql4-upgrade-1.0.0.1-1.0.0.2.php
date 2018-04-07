<?php

$installer = $this;

$catalogEavSetup = new Mage_Catalog_Model_Resource_Eav_Mysql4_Setup('core_setup');
$catalogEavSetup->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'disable_vendor', array(
	'group'     => 'General Information',
	'label'     => 'Do not allow vendor to choose',
    'type'      => 'int',
    'input'     => 'select',
    'source'    => 'eav/entity_attribute_source_boolean',
    'visible'   => true,
    'required'  => false,
    'position'  => 10,
));


$this->getConnection()->addColumn($this->getTable('catalog/product'), 'vendor_sku', 'text DEFAULT NULL AFTER sku');

$installer->endSetup(); 