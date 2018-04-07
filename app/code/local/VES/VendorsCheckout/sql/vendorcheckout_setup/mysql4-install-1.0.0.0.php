<?php

$installer = $this;

$this->getConnection()->addColumn($this->getTable('sales/quote'), 'vendor_id', 'int(10) UNSIGNED NOT NULL AFTER entity_id');
$this->getConnection()->addColumn($this->getTable('sales/quote'), 'parent_quote', 'int(10)  NOT NULL DEFAULT 0 AFTER vendor_id');

$this->getConnection()->addColumn($this->getTable('sales/quote_item'), 'vendor_id', 'int(10) UNSIGNED NOT NULL AFTER item_id');
$this->getConnection()->addColumn($this->getTable('sales/quote_item'), 'parent_item', 'int(10)  NOT NULL DEFAULT 0 AFTER vendor_id');

$this->getConnection()->addColumn($this->getTable('sales/order'), 'vendor_id', 'int(10) UNSIGNED DEFAULT NULL AFTER entity_id');

$this->getConnection()->addColumn($this->getTable('sales/invoice'), 'vendor_id', 'int(10) UNSIGNED DEFAULT NULL AFTER entity_id');
$this->getConnection()->addColumn($this->getTable('sales/shipment'), 'vendor_id', 'int(10) UNSIGNED DEFAULT NULL AFTER entity_id');
$this->getConnection()->addColumn($this->getTable('sales/creditmemo'), 'vendor_id', 'int(10) UNSIGNED DEFAULT NULL AFTER entity_id');
$this->getConnection()->addColumn($this->getTable('sales/invoice_grid'), 'vendor_id', 'int(10) UNSIGNED DEFAULT NULL AFTER entity_id');

$this->getConnection()->addColumn($this->getTable('sales/order_grid'), 'vendor_id', 'int(10) UNSIGNED DEFAULT NULL AFTER entity_id');
$this->getConnection()->addColumn($this->getTable('sales/invoice_grid'), 'vendor_id', 'int(10) UNSIGNED DEFAULT NULL AFTER entity_id');
$this->getConnection()->addColumn($this->getTable('sales/shipment_grid'), 'vendor_id', 'int(10) UNSIGNED DEFAULT NULL AFTER entity_id');
$this->getConnection()->addColumn($this->getTable('sales/creditmemo_grid'), 'vendor_id', 'int(10) UNSIGNED DEFAULT NULL AFTER entity_id');


$installer->endSetup();