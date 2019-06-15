<?php 
return array (
  'log_id' => 
  array (
    'name' => 'log_id',
    'type' => 'bigint(16) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => true,
    'autoinc' => true,
  ),
  'admin_id' => 
  array (
    'name' => 'admin_id',
    'type' => 'int(10)',
    'notnull' => false,
    'default' => '-1',
    'primary' => false,
    'autoinc' => false,
  ),
  'log_info' => 
  array (
    'name' => 'log_info',
    'type' => 'text',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'log_ip' => 
  array (
    'name' => 'log_ip',
    'type' => 'varchar(30)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'log_url' => 
  array (
    'name' => 'log_url',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'log_time' => 
  array (
    'name' => 'log_time',
    'type' => 'int(11)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
);