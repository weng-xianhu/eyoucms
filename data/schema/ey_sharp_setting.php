<?php 
return array (
  'key' => 
  array (
    'name' => 'key',
    'type' => 'varchar(30)',
    'notnull' => false,
    'default' => '',
    'primary' => true,
    'autoinc' => false,
  ),
  'describe' => 
  array (
    'name' => 'describe',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'values' => 
  array (
    'name' => 'values',
    'type' => 'mediumtext',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'update_time' => 
  array (
    'name' => 'update_time',
    'type' => 'int(11) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
);