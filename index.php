#!/usr/local/zend/bin/php
<?php
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(__DIR__);
define('__ROOT__',dirname(__DIR__));
//ini_set('display_errors', 0);
// Setup autoloading
require 'init_autoloader.php';
// Run the application!
Zend\Mvc\Application::init(require 'config/application.config.php')->run();
