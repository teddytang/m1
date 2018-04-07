<?php

$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE `{$installer->getTable('dataflow/profile')}` ADD `is_ves_marketplace` TINYINT( 1 ) NOT NULL DEFAULT '0';
ALTER TABLE `{$installer->getTable('dataflow/profile_history')}` ADD `vendor_id` int(11) unsigned NOT NULL DEFAULT '0';
");

$installer->endSetup(); 