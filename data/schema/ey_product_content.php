<?php 
return array (
  'id' => 
  array (
    'name' => 'id',
    'type' => 'int(10)',
    'notnull' => false,
    'default' => NULL,
    'primary' => true,
    'autoinc' => true,
  ),
  'aid' => 
  array (
    'name' => 'aid',
    'type' => 'int(10)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'content' => 
  array (
    'name' => 'content',
    'type' => 'longtext',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'add_time' => 
  array (
    'name' => 'add_time',
    'type' => 'int(11)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'update_time' => 
  array (
    'name' => 'update_time',
    'type' => 'int(11)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'fxrq' => 
  array (
    'name' => 'fxrq',
    'type' => 'enum(\'2019年\',\'2018年\',\'2017年\')',
    'notnull' => false,
    'default' => '2019年',
    'primary' => false,
    'autoinc' => false,
  ),
  'jiawei' => 
  array (
    'name' => 'jiawei',
    'type' => 'enum(\'0-1000\',\'1000-1699\',\'1700-2799\',\'2800-3500\',\'3500-10000\')',
    'notnull' => false,
    'default' => '0-1000',
    'primary' => false,
    'autoinc' => false,
  ),
  'yanse' => 
  array (
    'name' => 'yanse',
    'type' => 'enum(\'银色\',\'绿色\',\'黑色\',\'灰色\')',
    'notnull' => false,
    'default' => '银色',
    'primary' => false,
    'autoinc' => false,
  ),
);