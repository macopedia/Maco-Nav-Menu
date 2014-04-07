<?php

$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()->newTable($installer->getTable('EasyMenu/EasyMenu'))
    ->addColumn(
        'id', Varien_Db_Ddl_Table::TYPE_INTEGER , 11, array(
            'primary'  => true,
            'auto_increment' => true,
            'nullable'  => 'false',
        ), 'ID'
    )
    ->addColumn(
        'name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50, array(
            'nullable' => false,
        ), 'Name'
    )
    ->addColumn(
        'parent', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array(
            'nullable' => false,
            'default'   => 0,
        ), 'Parent ID'
    )
    ->addColumn(
        'type', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array(
            'nullable' => false,
        ), 'type'
    )
    ->addColumn(
        'value', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable' => true,
        ), 'type'
    )
    ->addColumn(
        'priority', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array(
            'nullable' => false,
        ), 'priority'
    )->addColumn(
        'store', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array(
            'nullable'  => true,
            'comment'   => 'Store Id',
        ), 'store'
    )
    ->setComment('Easymenu table');
$installer->getConnection()->createTable($table);

$installer->endSetup();