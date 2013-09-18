<?php defined('SYSPATH') or die('No direct script access.');

ini_set('include_path', ini_get('include_path').PATH_SEPARATOR.MODPATH . 'youtube/classes');
require_once MODPATH . 'youtube/classes/Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();