<?php

$installer = $this;
/* Add credit amount for vendor */

$this->addAttribute('ves_vendor', 'is_sendmail_active_vendor', array(
        'type'              => 'static',
        'label'             => 'Is send mail active vendor',
        'input'             => 'date',
		'frontend_input'	=> 'date',
        'class'             => '',
        'backend'           => '',
        'frontend'          => '',
        'source'            => '',
        'required'          => false,
        'user_defined'      => false,
        'unique'            => false,
));
$this->getConnection()->addColumn($this->getTable('vendors/vendor'), 'is_sendmail_active_vendor', 'smallint(5) unsigned DEFAULT 0');

$installer->endSetup(); 