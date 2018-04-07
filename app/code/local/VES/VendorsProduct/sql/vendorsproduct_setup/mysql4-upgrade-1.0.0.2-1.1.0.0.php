<?php

$installer = $this;

/*
 * Table Attribute Sets
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('vendorsproduct/attribute_set'))
    ->addColumn('attribute_set_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Attribute set id')
    ->addColumn('parent_set_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Parent Attribute Set Id')
    ->addColumn('attribute_set_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable'  => false,
            'default'   => '',
        ), 'Attribute Set Name')

    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_SMALLINT, 6, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Sort order')
    ->addForeignKey(
        $installer->getFkName(
            'vendorsproduct/attribute_set',
            'parent_set_id',
            'eav/attribute_set',
            'attribute_set_id'
        ),
        'parent_set_id', $installer->getTable('eav/attribute_set'), 'attribute_set_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ;
$installer->getConnection()->createTable($table);

/*
 * Table Attribute Group
 */
$table = $installer->getConnection()
->newTable($installer->getTable('vendorsproduct/attribute_group'))
->addColumn('attribute_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, array(
    'identity'  => true,
    'unsigned'  => true,
    'nullable'  => false,
    'primary'   => true,
), 'Attribute set id')
->addColumn('attribute_set_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, array(
    'unsigned'  => true,
    'nullable'  => false,
    'default'   => '0',
), 'Attribute Set Id')
->addColumn('attribute_group_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable'  => false,
    'default'   => '',
), 'Attribute Group Name')

->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_SMALLINT, 6, array(
    'nullable'  => false,
    'default'   => '0',
), 'Sort order')
->addForeignKey(
    $installer->getFkName(
        'vendorsproduct/attribute_group',
        'attribute_set_id',
        'vendorsproduct/attribute_set',
        'attribute_set_id'
    ),
    'attribute_set_id', $installer->getTable('vendorsproduct/attribute_set'), 'attribute_set_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
;
$installer->getConnection()->createTable($table);


/*
 * Table Attribute Group
 */
$table = $installer->getConnection()
->newTable($installer->getTable('vendorsproduct/entity_attribute'))
->addColumn('entity_attribute_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'identity'  => true,
    'unsigned'  => true,
    'nullable'  => false,
    'primary'   => true,
), 'Attribute set id')
->addColumn('attribute_set_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, array(
    'unsigned'  => true,
    'nullable'  => false,
    'default'   => '0',
), 'Attribute Set Id')
->addColumn('attribute_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, array(
    'unsigned'  => true,
    'nullable'  => false,
    'default'   => '0',
), 'Attribute Group Id')
->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, array(
    'unsigned'  => true,
    'nullable'  => false,
    'default'   => '0',
), 'Attribute Id')

->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_SMALLINT, 6, array(
    'nullable'  => false,
    'default'   => '0',
), 'Sort order')
->addForeignKey(
    $installer->getFkName(
        'vendorsproduct/entity_attribute',
        'attribute_set_id',
        'vendorsproduct/attribute_set',
        'attribute_set_id'
    ),
    'attribute_set_id', $installer->getTable('vendorsproduct/attribute_set'), 'attribute_set_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
->addForeignKey(
    $installer->getFkName(
        'vendorsproduct/entity_attribute',
        'attribute_group_id',
        'vendorsproduct/attribute_group',
        'attribute_group_id'
    ),
    'attribute_group_id', $installer->getTable('vendorsproduct/attribute_group'), 'attribute_group_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
;

$installer->getConnection()->createTable($table);

$installer->endSetup(); 