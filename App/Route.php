<?php

namespace Monster\App;

use Monster\App\Core;

class Route
{
    // An array of all route items
    private static $routes = [];

    // A prefix to prepend to all route paths
    private static $prefix = '';

    public static function get($path, $controller, $prefix = null)
    {
        // Create a new route item with the GET method
        self::$routes[] = new Core('GET', self::prefixPath($path, $prefix), $controller);
    }

    public static function post($path, $controller, $prefix = null)
    {
        // Create a new route item with the POST method
        self::$routes[] = new Core('POST', self::prefixPath($path, $prefix), $controller);
    }

    public static function put($path, $controller, $prefix = null)
    {
        // Create a new route item with the PUT method
        self::$routes[] = new Core('PUT', self::prefixPath($path, $prefix), $controller);
    }

    public static function delete($path, $controller, $prefix = null)
    {
        // Create a new route item with the DELETE method
        self::$routes[] = new Core('DELETE', self::prefixPath($path, $prefix), $controller);
    }

    public static function any($path, $controller, $prefix = null)
    {
        // Create a new route item with any HTTP method
        self::$routes[] = new Core('ANY', self::prefixPath($path, $prefix), $controller);
    }

    public static function options($path, $controller, $prefix = null)
    {
        // Create a new route item with the OPTIONS method
        self::$routes[] = new Core('OPTIONS', self::prefixPath($path, $prefix), $controller);
    }

    public static function patch($path, $controller, $prefix = null)
    {
        // Create a new route item with the PATCH method
        self::$routes[] = new Core('PATCH', self::prefixPath($path, $prefix), $controller);
    }

    public static function group($prefix, $callback)
    {
        // Set the global prefix for all routes within the callback function
        $previousPrefix = self::$prefix;
        self::setPrefix(self::prefixPath($prefix));
        $callback();
        self::setPrefix($previousPrefix);
    }

    public static function run()
    {
        $request_method = $_SERVER['REQUEST_METHOD'];
        $request_uri = $_SERVER['REQUEST_URI'];

        // Use a fast routing algorithm, such as the Radix tree, to match the request URI with the route items
        $matchedRoute = null;

        foreach (self::getRoutes() as $route) {
            if ($route->match($request_method, $request_uri)) {
                // If a route matches, check if it has parameters and continue searching for matches
                if (!empty($route->getParams())) {
                    $matchedRoute = $route;
                    continue;
                }

                // If a route matches and has no parameters, execute it immediately
                $route->execute();
                return;
            }
        }

        // If a route with parameters matched, execute it
        if ($matchedRoute !== null) {
            $matchedRoute->execute();
            return;
        }

        // If no route matches, return a 404 error
        http_response_code(404);
        include_once(__DIR__ . "/../routes/errors/404.html");
    }

    // Getters and setters for the private properties
    public static function getRoutes()
    {
        return self::$routes;
    }

    public static function setRoutes($routes)
    {
        self::$routes = $routes;
    }

    public static function setPrefix($prefix)
    {
        self::$prefix = $prefix;
    }

    public static function getPrefix()
    {
        return self::$prefix;
    }

    // Helper function to prefix a path with the global prefix

    public static function prefixPath($path, $prefix = null)
    {
        if (empty($prefix)) {
            $prefix = self::$prefix;
        }

        if ($path === '/') {
            return $prefix;
        }

        return $prefix . $path;
    }
}
