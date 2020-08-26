<?php

namespace Edev\Route;

use Edev\System\Helpers\Arr;

class Route
{
    protected static $_roots = []; // container array for routes
    protected static $instance;

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new Route();
            self::_generateHomeRoute();
        }
        return self::$instance;
    }

    /**
     * Direct method to generate the index pointer to HomeController
     *
     * @return void
     */
    private static function _generateHomeRoute()
    {
        self::add('/', 'HomeController', 'index', 'GET', null);
    }

    /**
     * Resource method adds all seven default HTTP response
     *
     * @param string $root
     * @param string $controllerName
     * @param string $namespace
     * @return void
     */
    public static function resource(string $root, string $controllerName, string $namespace = null)
    {
        self::add("$root", $controllerName, 'index', 'GET', $namespace);
        self::add("$root", $controllerName, 'store', 'POST', $namespace);
        self::add("$root/create", $controllerName, 'create', 'GET', $namespace);
        self::add("$root/{id}", $controllerName, 'show', 'GET', $namespace);
        self::add("$root/{id}/edit", $controllerName, 'edit', 'GET', $namespace);
        self::add("$root/{id}", $controllerName, 'update', 'PUT', $namespace);
        self::add("$root/{id}", $controllerName, 'destroy', 'DELETE', $namespace);
    }

    /**
     * Add custom controller-method combos to extend routes
     *
     * @param string $route
     * @param string $controllerName
     * @param string $methodName
     * @param string $verb
     * @return void
     */
    public static function controller(string $route, string $controllerName, string $methodName, string $verb = 'GET')
    {
        self::add($route, $controllerName, $methodName, $verb, null);
    }

    /**
     * Check if the root already exists in the _roots array
     *
     * @param string $root
     * @return void
     */
    private static function _rootExists(string $root)
    {
        return key_exists($root, self::$_roots);
    }

    /**
     * Method to add routes to the _roots array
     *
     * @param string $route
     * @param string $controller
     * @param string $method
     * @param string $verb
     * @param string $namespace
     * @return void
     */
    protected static function add(string $route, string $controller, string $method, string $verb, string $namespace = null)
    {
        $root = self::_parseRoot($route);
        $activeRoute = self::_getActiveRoot($root, $controller, $namespace);
        $activeRoute->addNode($route, $method, $verb);
    }

    /**
     * Get existing Route/RouteRoot instance or get a new
     *
     * @param string $root
     * @param string $controller
     * @return void
     */
    private static function _getActiveRoot($root, $controller, $namespace): \Edev\Route\RouteRoot
    {
        if (!self::_rootExists($root)) {
            self::$_roots[$root] = self::_newRoute(new \Edev\Route\RouteRoot($controller, $namespace));
        }
        return self::$_roots[$root];
    }

    /**
     * Get the root string of a route string
     *
     * @param string $route
     * @return string
     */
    private static function _parseRoot($route): string
    {
        if ($route == '/') {
            return $route;
        }
        return trim(current(explode('/', $route)), '/');
    }

    /**
     * DI for a new Root object
     *
     * @param \Route\RouteRoot $routeRoot
     * @return \Route\RouteRoot
     */
    private static function _newRoute(\Edev\Route\RouteRoot $routeRoot): \Edev\Route\RouteRoot
    {
        return $routeRoot;
    }

    /**
     * Debug output method
     *
     * @return void
     */
    public static function dump()
    {
        echo '<pre>';
        print_r(self::$_roots);
    }

    /**
     * Does the work to generate the controller from the input route
     *
     * @param string $route
     * @param array $data
     * @return void
     */
    public static function submit(string $route, array $data)
    {
        $root = self::_parseRoot($route);
        $controller = self::_getController(self::_getRoot($root));
        $method = self::_getMethod(self::_getRoot($root), $route, $_SERVER['REQUEST_METHOD']);
        $props = self::_getRoot($root)->getProps();
        return self::_generateController($controller, $method, $data, $props);
    }

    /**
     * Creates and inits the controller::method associated w/ the specified route
     *
     * @param string $controller
     * @param string $method
     * @param array $data
     * @param array $props
     * @return boolean
     */
    private static function _generateController(string $controller, string $method, array $data = [], array $props = [])
    {
        $class = new $controller();
        $class->init();
        $class->setData($data);
        $class->setProps(array_merge($props, $data));
        $class->$method();

        return true;
    }

    /**
     * Returns a RouteRoot class based on the root of the input route
     *
     * @param [type] $root
     * @return \Route\RouteRoot
     */
    private static function _getRoot($root): \Edev\Route\RouteRoot
    {
        $route = self::$_roots[$root];
        if ($route instanceof \Edev\Route\RouteRoot) {
            return $route;
        }
        http_response_code(404);
        throw new \Exception\RouteNotFound();
    }

    /**
     * Private getter to get the controller name
     *
     * @param \Route\RouteRoot $routeRoot
     * @return string
     */
    private static function _getController($routeRoot): string
    {
        if (is_null($routeRoot)) {
            throw new \Exception\RouteNotFound('Missing route:');
        }
        return $routeRoot->getController();

    }

    /**
     * Return the method based on the Root object, route and verb
     *
     * @param \Route\RouteRoot $routeRoot
     * @param string $route
     * @param string $verb
     * @return string
     */
    private static function _getMethod(\Edev\Route\RouteRoot $routeRoot, string $route, string $verb): string
    {
        return $routeRoot->getMethod($route, $verb);
    }

    public static function overrideExistingRoute($route, $controller, $method, $verb)
    {
        $root = self::_parseRoot($route);
        $activeRoot = self::_getRoot($root);
        $activeRoot->overrideController($controller);
        $activeRoot->overrideMethod($route, $verb, $method);
    }
}
