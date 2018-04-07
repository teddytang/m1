<?php

$installer = $this;
/**
 * Create table 'vendor_transaction'
 */
$sql = "
    DROP TABLE IF EXISTS `{$this->getTable('vendorscommission/rule')}`;
    CREATE TABLE IF NOT EXISTS `{$this->getTable('vendorscommission/rule')}` (
    `rule_id` int(10) unsigned NOT NULL COMMENT 'Commisison Rule Id',
    `website_ids` varchar(128) NOT NULL COMMENT 'Website Ids',
    `vendor_group_ids` varchar(128) NOT NULL COMMENT 'Vendor Group Ids',
    `name` varchar(32) NOT NULL COMMENT 'Rule Name',
    `description` text NOT NULL COMMENT 'Rule Description',
    `from_date` date  NULL COMMENT 'From Date',
    `to_date` date NULL COMMENT 'To Date',
    `is_active` smallint(6) NOT NULL COMMENT 'Is Active',
    `conditions_serialized` text NULL COMMENT 'Conditions Serialized',
    `stop_rules_processing` smallint(6) NOT NULL COMMENT 'Stop rules processing',
    `priority` int(11) NOT NULL COMMENT 'Rule Priority',
    `commission_by` varchar(32) NOT NULL COMMENT 'Commision By',
    `commission_action` varchar(32) NOT NULL COMMENT 'Commision Type',
    `commission_amount` decimal(12,4) NOT NULL COMMENT 'Commision amount'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='ves_vendor_credit_commission_rule';

ALTER TABLE `{$this->getTable('vendorscommission/rule')}`
 ADD PRIMARY KEY (`rule_id`), ADD KEY `IDX_VENDORSCOMMISSIONRULE_IS_ACTIVE_SORT_ORDER_TO_DATE_FROM_DATE` (`is_active`,`priority`,`to_date`,`from_date`);
 
ALTER TABLE `{$this->getTable('vendorscommission/rule')}`
MODIFY `rule_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Rule Id';
";


$installer->startSetup();
$installer->run($sql);
$installer->endSetup(); 