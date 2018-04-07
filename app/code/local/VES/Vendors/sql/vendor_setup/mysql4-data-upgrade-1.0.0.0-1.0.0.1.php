<?php 
$installer = $this;

/*Get all duplicate content*/
$resource = Mage::getSingleton('core/resource');
$readConnection = $resource->getConnection('core_read');
$query = array(
	$installer->getTable(array('vendors/vendor', 'char')) 		=> 'SELECT * FROM ' . $installer->getTable(array('vendors/vendor', 'char')),
	$installer->getTable(array('vendors/vendor', 'datetime')) 	=> 'SELECT * FROM ' . $installer->getTable(array('vendors/vendor', 'datetime')),
	$installer->getTable(array('vendors/vendor', 'decimal')) 	=> 'SELECT * FROM ' . $installer->getTable(array('vendors/vendor', 'decimal')),
	$installer->getTable(array('vendors/vendor', 'int')) 		=> 'SELECT * FROM ' . $installer->getTable(array('vendors/vendor', 'int')),
	$installer->getTable(array('vendors/vendor', 'varchar')) 	=> 'SELECT * FROM ' . $installer->getTable(array('vendors/vendor', 'varchar')),
);
$delete = array();
$data	= array();

foreach($query as $table=>$query){
	if(!isset($data[$table])) $data[$table]= array();
	if(!isset($delete[$table])) $delete[$table]= array();
	$results = $readConnection->fetchAll($query);
	if(sizeof($results)){
		foreach($results as $row){
			$key = $row['entity_id'].'_'.$row['attribute_id'].'_'.$row['store_id'];
			if(!isset($data[$table][$key])) {
				$data[$table][$key] 	= $row['value_id'];
			}else{
				$delete[$table][$key][] 	= $data[$table][$key];
				$data[$table][$key] 	= $row['value_id'];
			}
		}
	}
}

$installer->startSetup();

/*Delete Duplicate content*/
$sql = ''; 
foreach($delete as $tableName=>$deleteItems){
	if(is_array($deleteItems) && sizeof($deleteItems)) foreach($deleteItems as $key=>$valueIds){
		foreach($valueIds as $valueId) $sql .= "DELETE FROM `{$tableName}` WHERE `value_id` = {$valueId};";
	}
}

$installer->run($sql);



/*Add unique key*/
$sql = "
	ALTER TABLE {$installer->getTable(array('vendors/vendor', 'char'))} ADD CONSTRAINT UNQ_VENDOR_ENTT_CHR_ENTT_ID_ATTR_ID_STORE_ID UNIQUE (`entity_id`,`attribute_id`,`store_id`);
	ALTER TABLE {$installer->getTable(array('vendors/vendor', 'datetime'))} ADD CONSTRAINT UNQ_VENDOR_ENTT_DATETIME_ENTT_ID_ATTR_ID_STORE_ID UNIQUE (`entity_id`,`attribute_id`,`store_id`);
	ALTER TABLE {$installer->getTable(array('vendors/vendor', 'decimal'))} ADD CONSTRAINT UNQ_VENDOR_ENTT_DECI_ENTT_ID_ATTR_ID_STORE_ID UNIQUE (`entity_id`,`attribute_id`,`store_id`);
	ALTER TABLE {$installer->getTable(array('vendors/vendor', 'int'))} ADD CONSTRAINT UNQ_VENDOR_ENTT_INT_ENTT_ID_ATTR_ID_STORE_ID UNIQUE (`entity_id`,`attribute_id`,`store_id`);
	ALTER TABLE {$installer->getTable(array('vendors/vendor', 'varchar'))} ADD CONSTRAINT UNQ_VENDOR_ENTT_VARCHR_ENTT_ID_ATTR_ID_STORE_ID UNIQUE (`entity_id`,`attribute_id`,`store_id`);
	ALTER TABLE {$installer->getTable(array('vendors/vendor', 'text'))} ADD CONSTRAINT UNQ_VENDOR_ENTT_TEXT_ENTT_ID_ATTR_ID_STORE_ID UNIQUE (`entity_id`,`attribute_id`,`store_id`);
";
$installer->run($sql);


$installer->endSetup();