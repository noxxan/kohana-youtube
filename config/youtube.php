<?php defined('SYSPATH') OR die('No direct access allowed.');

$config = array();

$config['auth_url'] = 'https://www.google.com/accounts/ClientLogin';
$config['username'] = 'googleaccount@gmail.com';
$config['password'] = 'pass';
$config['dev_key'] = '';
$config['upload_url'] = 'http://uploads.gdata.youtube.com/feeds/api/users/default/uploads';

$config['source'] = 'Project name';

return $config;