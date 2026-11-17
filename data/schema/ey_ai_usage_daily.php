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
  'day' => 
  array (
    'name' => 'day',
    'type' => 'int(11)',
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
  'model_id' => 
  array (
    'name' => 'model_id',
    'type' => 'int(10) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'calls' => 
  array (
    'name' => 'calls',
    'type' => 'int(10) unsigned',
    'notnull' => false,
    'default' => '0',
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
);