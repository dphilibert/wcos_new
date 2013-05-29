<?php

if (function_exists('date_default_timezone_set')) 
  date_default_timezone_set('Europe/Berlin');
  
// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode (PATH_SEPARATOR, array(
    '/home/daniel/typo3-extensions/elektroniknet/config_elektroniknet/library',
    get_include_path(),
)));

//Define uploads-path
defined('UPLOAD_PATH')
    || define('UPLOAD_PATH', (getenv('UPLOAD_PATH') ? getenv('UPLOAD_PATH') : $_SERVER ['DOCUMENT_ROOT'].'/uploads/')); 
 
/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()
            ->run();
