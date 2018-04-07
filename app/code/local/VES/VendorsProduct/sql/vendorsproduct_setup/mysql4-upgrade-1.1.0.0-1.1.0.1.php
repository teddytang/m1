<?php

$installer = $this;

$this->getConnection()->addColumn($this->getTable('vendorsproduct/attribute_set'), 'group_display_type', 'varchar(32) NOT NULL DEFAULT "" AFTER attribute_set_name');

$installer->endSetup(); 