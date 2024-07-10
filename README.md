# E-Procurement BGR v1

## Files below have to create by your own

- php.env

```php
<?php
define('CI_ENV','[development/production]');
```

- application/config/database.php

```php
<?php
defined('BASEPATH') or exit('No direct script access allowed');
$active_group = 'default';
$query_builder = TRUE;

$db['default'] = array(
	'dsn'	=> '',
	'hostname' => '',
	'username' => '',
	'password' => '',
	'port' => '',
	'database' => '',
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => TRUE,
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);

```
