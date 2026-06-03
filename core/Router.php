<?php
/**
 * Simple Router Class
 * Routes requests to the appropriate controller and method
 */

class Router
{
    private $routes = [];

    /**
     * Set all routes
     */
    public function setRoutes(array $routes)
    {
        $this->routes = $routes;
    }

    /**
     * Dispatch the request to the correct controller/method
     */
    public function dispatch($action)
    {
        // Check if route exists
        if (!isset($this->routes[$action])) {
            $this->handleNotFound($action);
            return;
        }

        // Get controller class name and method name
        $route = $this->routes[$action];
        $controllerName = $route[0];
        $methodName = $route[1];

        // Build path to controller file
        $controllerFile = __DIR__ . '/../controllers/' . $controllerName . '.php';

        // Check if controller file exists
        if (!file_exists($controllerFile)) {
            $this->handleError("Controller file not found: {$controllerName}.php");
            return;
        }

        // Load the controller file
        require_once $controllerFile;

        // Check if controller class exists
        if (!class_exists($controllerName)) {
            $this->handleError("Controller class not found: {$controllerName}");
            return;
        }

        // Create controller instance
        $controller = new $controllerName();

        // Check if method exists
        if (!method_exists($controller, $methodName)) {
            $this->handleError("Method not found: {$controllerName}->{$methodName}()");
            return;
        }

        // Call the method
        $controller->$methodName();
    }

    /**
     * Handle 404 Not Found
     */
    private function handleNotFound($action)
    {
        http_response_code(404);
        echo "404 - Page not found: " . htmlspecialchars($action);
    }

    /**
     * Handle errors
     */
    private function handleError($message)
    {
        http_response_code(500);
        echo "Error: " . htmlspecialchars($message);
    }
}
