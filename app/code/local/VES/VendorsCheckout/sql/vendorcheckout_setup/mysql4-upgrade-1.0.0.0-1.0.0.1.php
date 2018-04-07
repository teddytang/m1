<?php

$installer = $this;

$this->getConnection()->addColumn($this->getTable('sales/order_item'), 'vendor_id', 'int(10) UNSIGNED DEFAULT NULL AFTER item_id');
$this->getConnection()->addColumn($this->getTable('sales/quote_address'), 'vendor_id', 'int(10) UNSIGNED NOT NULL AFTER address_id');

$installer->endSetup();