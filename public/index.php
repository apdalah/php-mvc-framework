<?php

/**
 * Front Controller
 * 
 * php version 7.3.8
*/

/**
 * Twig Template Engin 
*/

require_once dirname(__DIR__) .  '/vendor/autoload.php';

/**
 * Error and Exception handling
*/
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');


$router = new Core\Router();

/**
 * Router examples
 */
$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('{controller}/{action}');
$router->add('{controller}/{id:\d+}/{action}');
$router->add('admin/{controller}/{action}', ['namespace' => 'Admin']);

/**
 * create the controller and the action automatically
 */
$router->dispatch($_SERVER['QUERY_STRING']);




