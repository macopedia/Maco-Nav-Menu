<?php

$installer = $this;
$installer->startSetup();

$table = $installer->getTable('EasyMenu/EasyMenu');

$installer->getConnection()->addColumn(
    $table,
    'store',
    array(
        'nullable'  => false,
        'type'  => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment'   => 'Store Id',
        'length'    => 11,
        'default'   => 0,
    )
);
$installer->endSetup();