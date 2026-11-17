<?php 
return array (
  'id' => 
  array (
    'name' => 'id',
    'type' => 'int(10) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => true,
    'autoinc' => true,
  ),
  'model_id' => 
  array (
    'name' => 'model_id',
    'type' => 'int(10) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'capability' => 
  array (
    'name' => 'capability',
    'type' => 'varchar(20)',
    'notnull' => false,
    'default' => 'chat',
    'primary' => false,
    'autoinc' => false,
  ),
  'scene' => 
  array (
    'name' => 'scene',
    'type' => 'varchar(50)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'usage_num' => 
  array (
    'name' => 'usage_num',
    'type' => 'int(10) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'cost_est' => 
  array (
    'name' => 'cost_est',
    'type' => 'decimal(10,4)',
    'notnull' => false,
    'default' => '0.0000',
    'primary' => false,
    'autoinc' => false,
  ),
  'ip' => 
  array (
    'name' => 'ip',
    'type' => 'varchar(46)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'create_time' => 
  array (
    'name' => 'create_time',
    'type' => 'int(11)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
);