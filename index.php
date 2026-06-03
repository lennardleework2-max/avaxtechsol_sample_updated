<?php
/**
 * Front Controller - Routes all requests
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/core/Router.php';

// Get action from URL, default to 'dashboard'
$action = isset($_GET['action']) ? $_GET['action'] : 'dashboard';

// Create router and load routes
$router = new Router();
$routes = require __DIR__ . '/routes.php';
$router->setRoutes($routes);

// Dispatch the request
$router->dispatch($action);
