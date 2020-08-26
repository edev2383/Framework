<?php

namespace Edev\Route;

use Edev\System\Helpers\Arr;

class RouteRoot
{

    protected $controller;
    protected $_nodes = [];
    protected $_variableNodes = [];
    protected $_foundMatch;

    public function __construct(string $controller, string $namespace = null)
    {
        $this->_setController($controller);
        $this->_setNamespace($namespace);
    }

    /**
     * getter for the controller method. Adds the namespace to the controller
     *
     * @return string
     */
    public function getController(): string
    {

        return $this->namespace ? '\\Edev\\Controller\\' . $this->namespace . '\\' . $this->controller : '\\Edev\\Controller\\' . $this->controller;
    }

    /**
     * getter for the specific method the route poitns to
     *
     * @param string $route
     * @param string $verb
     * @return string
     */
    public function getMethod(string $route, string $verb): string
    {

        // echo "[ route: $route, verb: $verb ]";
        $this->matches = [];
        // direct comparison. If the route exists in _nodes, it's a direct comp and we can return the method
        if (array_key_exists($route, $this->_nodes)) {
            return $this->_getNode($route, $verb)->getMethod();
        } else {

            // If not, we need to compare the incoming route against existing routes and see if anything
            // lines up with our expectations
            $this->_foundMatch = $this->_newMatch(new \Edev\Route\RouteMatch($this->_variableNodes, $route));

            if (isset($_GET['flow'])) {
                // Arr::pre($this);
                // echo '<h2>' . __FUNCTION__ . '</h2>';
                // echo "[ route: $route ] [ verb: $verb ]";
                // Arr::pre($this->_nodes);
                // echo '_____________________________________________________________--';
                // Arr::pre($this->_foundMatch);
            }

            return $this->_getNode($this->_foundMatch->getRouteMatch(), $verb)->getMethod();
        }
    }

    /**
     * getter to pass inline props
     *
     * @return array
     */
    public function getProps(): array
    {
        return $this->_foundMatch ? $this->_foundMatch->getProps() : [];
    }

    /**
     * adds a new node
     *
     * @param string $route
     * @param string $methodName
     * @param string $verb
     * @return void
     */
    public function addNode(string $route, string $methodName, string $verb): void
    {
        if (isset($this->_nodes[$route][$verb])) {
            die("Attempting to overwrite ::_nodes['$route']['$verb']. If you want to overwrite a node, use RouteRoot::replaceNode(). Terminating script.");
        }
        $this->_nodes[$route][$verb] = $this->_newNode(new \Edev\Route\RouteNode($methodName));
        $this->_parseVariableRoute($route);
    }

    /**
     * Adds route to the _variableNodes array. This array acts as a container
     * to hold onto any routes that contain a {variable}
     *
     * TODO: test w/ removing the {} for a more direct PROPS comparison
     *
     * @param string $route
     * @return void
     */
    private function _parseVariableRoute(string $route): void
    {
        if (preg_match('/\{(.*?)\}/', $route, $m)) {
            if (!in_array($route, $this->_variableNodes)) {
                $this->_variableNodes[] = $route;
            }
        }
    }

    /**
     * Dependency Injection for RouteNode class
     *
     * @param \Route\RouteNode $routeNode
     * @return \Route\RouteMatch
     */
    protected function _newNode(\Edev\Route\RouteNode $routeNode): \Edev\Route\RouteNode
    {
        return $routeNode;
    }

    /**
     * Dependency injection for RouteMatch object
     *
     * @param \Route\RouteMatch $routeMatch
     * @return \Route\RouteMatch
     */
    private function _newMatch(\Edev\Route\RouteMatch $routeMatch): \Edev\Route\RouteMatch
    {
        return $routeMatch;
    }

    /**
     * private getter for the RouteNode from the _nodes array, based on the route->verb combo
     *
     * @param [type] $route
     * @param [type] $verb
     * @return \Route\RouteNode
     */
    private function _getNode($route, $verb): \Edev\Route\RouteNode
    {
        if (isset($_GET['flow'])) {
            echo "[ route: $route ] [ verb: $verb ]";
            Arr::pre($this->_nodes);
        }
        if (is_null($route)) {
            if (isset($_GET['test'])) {
                // ! DEBUG
                echo '<h3> route is null </h3>
                <p>Here is the $_SERVER request obj</p><pre>';
                print_r($_SERVER);
            } else {
                http_response_code(404);
                throw new \Exception\RouteNotFound();
            }
        }

        return $this->_nodes[$route][$verb];
    }

    /**
     * Sets controller class for the route object
     *
     * @param [type] $controller
     * @return void
     */
    private function _setController($controller): void
    {
        $this->controller = $controller;
    }

    /**
     * sets namespace for the route object
     *
     * @param string $namespace
     * @return void
     */
    private function _setNamespace(string $namespace = null): void
    {
        $this->namespace = $namespace;
    }

    public function overrideController($controller): void
    {
        $this->_setController($controller);
    }

    public function overrideMethod($route, $verb, $method)
    {
        $node = $this->_getNode($route, $verb);
        $node->overrideMethod($method);
    }
}
