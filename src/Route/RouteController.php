<?php

namespace Edev\Route;

use Exception;
use Ifs\RouteInterface as RouteInterface;

class AltRoute implements RouteInterface
{

    private static $instance = null;

    // array to hold requestable uris
    private static $_uri = [];

    // array to point to Controller::method()
    private static $_method = [];

    // array to hold requestable controllers
    private static $_controllers = [];

    // vars
    private static $_param;

    private static $_data;

    private static $_verb;

    private static $_namespace = [];

    // define HomeController values on creation
    private function __construct()
    {
        self::$_uri[] = '/';
        self::$_method['/'] = ['GET' => 'index', 'POST' => 'index'];
        self::$_controllers['/'] = 'HomeController';
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new Route();
        }

        return self::$instance;
    }

    /**
     * Create a single route that points to a specific method within the provided controller
     *
     * @param string $uri           - route root example.com/[uri]
     * @param string $controller    - controller name, must be PascalCase and map to ControllerName.php in Controller folder
     * @param string $method        - the method to be called via the uri, must be prefixed w/ lowercase $VERB
     * @param string $verb          - HTTP METHOD, must be UPPERCASE ['GET', 'POST', 'PUT', 'DELETE']
     *
     * @return void
     */
    public static function controller($uri, $controller, $method, $verb = 'GET')
    {

        // check that verb is contained with method name
        // TODO: regex to confirm location of verb within method name
        if (strpos($method, strtolower($verb)) !== false) {
            self::add($uri, $controller, $method, $verb);
        } else {
            // error throw exception
            // method suffix MUST match http verb
        }
    }

    // add a new uri route
    // some redundancy between this and controller(), allowing for expansion
    private static function add($uri, $controller, $method, $verb = 'GET')
    {

        self::createUri($uri, $controller);

        self::createController($uri, $controller);

        self::createMethod($uri, $method, $verb);
    }

    /**
     * Create a suite of routes around the uri string:
     *     Example: $uri = 'test';  // www.example.com/test is now a route
     *     Resource adds (7) routes to the application and point to specific methods within the associated Controller class.
     *
     *             Route           VERB            METHOD
     *         1.) /test           [GET]        => index
     *         2.) /test           [POST]       => store
     *         3.) /test/create    [GET]        => create
     *         4.) /test/{id}      [GET]        => show
     *         5.) /test/{id}/edit [GET]        => edit
     *         6.) /test/{id}      [PUT]        => update
     *         7.) /test/{id}      [DELETE]     => destroy
     *
     *         NOTES: {id} = id number associated with the record, see {...TODO ADD}
     *
     * @param string $uri           - route root example.com/[uri]
     * @param string $controller    - controller name, must be PascalCase and map to ControllerName.php in Controller folder
     * @param string $namespace     - additional namespace value for any Controller\* child classes
     * @return void
     */
    public static function resource($uri, $controller, $namespace = null)
    {

        // if namespace is set, add to _namespace array, we'll retrieve this later with getController();
        if (!is_null($namespace)) {
            self::$_namespace[$uri] = $namespace;
        }
        // echo $controller;
        // echo '<br /><br />';
        // Set Default Controller CRUD Resource Methods when resource is called
        self::add($uri, $controller, 'index', 'GET');
        self::add($uri, $controller, 'store', 'POST');
        if ($uri != '/') {
            self::add($uri . '/create', $controller, 'create', 'GET');
            self::add($uri . '/{id}', $controller, 'show', 'GET');
            self::add($uri . '/{id}', $controller, 'update', 'PUT');
            self::add($uri . '/{id}', $controller, 'destroy', 'DELETE');
            self::add($uri . '/{id}/edit', $controller, 'edit', 'GET');
        }
    }

    /**
     * submit() runs the logic of calling the controller
     * @param $uri
     * @param $data
     */
    public static function submit($uri, $data)
    {


        try {

            $ii = 0;
            self::$_verb = $_SERVER['REQUEST_METHOD'];
            self::$_param = $uri;
            self::$_data = $data;

            // loop through all _uris
            foreach (self::$_uri as $k => $uriValue) {
                $ii++;

                // first check for a simple match
                if (self::$_param == $uriValue) {

                    // echo '<pre>';
                    // print_r($this);
                    // echo '</pre>';

                    // define the Controller class & check if it exists
                    if ($class = self::getController(self::$_param)) {

                        // define method and check if exists
                        if ($method = self::getMethod(self::$_param, self::$_verb, $class)) {

                            self::generateController($class, $method, self::$_data);

                            // exit the loop
                            exit();
                        } else {
                            // Method does not exists
                            // TODO: error logging
                            throw new Exception\MethodNotFound('Method not found. Please try again.');
                        }
                    } else {

                        // Controller does not exist
                        // TODO: error logging
                        throw new Exception\ControllerNotFound('Controller not found. Please try again.');
                    }
                } else {

                    // if no simple match, check for complex match
                    $valueArr = explode('/', $uriValue);
                    $paramArr = explode('/', self::$_param);

                    // check that the roots are the same and that the arrays are the same length
                    // this is the easiest fastest way to eliminate non-matching
                    if ($valueArr[0] == $paramArr[0] && sizeof($valueArr) == sizeof($paramArr)) {

                        // holder for the eventual uri comparison
                        $trueUri = [];

                        // for each value of valueArr (from the Route object uris)
                        foreach ($valueArr as $key => $value) {

                            // compare each valueArr entry against the matching key in paramArr
                            if ($valueArr[$key] == $paramArr[$key]) {

                                // if they match, push the value to the trueUri array
                                $trueUri[] = $valueArr[$key];
                            } else if (self::isVar($valueArr[$key])) {
                                // if they don't match, but the value is an array (has {x} format)

                                // if it's a variable and it has the correct format
                                // default format begins with '0x' followed by numbers, letters and hyphens
                                // ex: 0x890-JKFJKD-0000  more secure and more unique
                                // this comparison goes against paramArr, not valueArr since
                                // that's the variable format to test
                                if (self::isType($paramArr[$key])) {

                                    // push the value to the trueUri array
                                    $trueUri[] = $valueArr[$key];
                                    self::$_data['props'][self::formatProp($valueArr[$key])] = $paramArr[$key];
                                } else {

                                    $formattedValue = $valueArr[$key];
                                    $formattedValue = str_replace('{', '', $formattedValue);
                                    $formattedValue = str_replace('}', '', $formattedValue);
                                    $propName = explode('::', $formattedValue)[0];
                                    // echo 'propName: ' . $propName;
                                    // echo '<br />';

                                    $propType = explode('::', $formattedValue)[1];
                                    // echo 'propType: ' . $propType;
                                    // echo '<br />';

                                    if (gettype($paramArr[$key]) == $propType) {
                                        $trueUri[] = $valueArr[$key];

                                        self::$_data[$propName] = $paramArr[$key];
                                    } else {

                                        // echo $paramArr[$key];
                                        // echo '<br />';
                                        // echo $valueArr[$key];
                                        // echo '<br />';
                                        // this break is not an exception, just a dead end in the loop
                                        break;
                                    }
                                }
                            } else {

                                // this break is not an exception, just a dead end in the loop
                                break;
                            }
                        }

                        // implode trueUir to string
                        $trueUri = implode('/', $trueUri);

                        // check that it matches uriValue
                        if ($trueUri == $uriValue) {

                            // define class and check that it exists
                            if ($class = self::getController(self::$_param)) {

                                // define method and check that it exists
                                if ($method = self::getMethod($trueUri, self::$_verb, $class)) {

                                    self::generateController($class, $method, self::$_data);

                                    // call the controller with method and attached data
                                    // new $class($method, self::$_data);
                                    // exit the loop
                                    exit();
                                } else {

                                    // Method does not exists
                                    // TODO: error logging
                                    throw new Exception\MethodNotFound('Error 404: Page Not Found. Method Does Not Exist');
                                }
                            } else {

                                // echo 'controller does not exists round 2 <br /><br />';
                                // Controller does not exist
                                // TODO: error logging
                                throw new Exception\ControllerNotFound('Error 404: Page Not Found. Controller Not Found.');
                            }
                        }
                    } else {
                        if ($ii >= count(self::$_uri)) {
                            throw new Exception\RouteNotFound('Error: 404 Page Not Found. No Corresponding route. ' . self::$_param);
                        }
                    }
                }
            } // end foreach loop of routes

            // !------------------------------------
            if (isset(self::$_data['xtc'])) {
            }
            echo '<pre>';
            //print_r($this);
        } catch (\Exception\MethodNotFound $e) {
            die($e->getMessage());
        } catch (\Exception\ControllerNotFound $e) {
            die($e->getMessage());
        } catch (\Exception\RouteNotFound $e) {
            die($e->getMessage());
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }

    private static function generateController($controller, $method, $data)
    {

        // instantiate the class
        $class = new $controller();

        // set the _data value // this will generate the props value as well
        $class->setData($data);

        // init calls connect and inits all props and repos
        $class->init();

        $class->$method();
    }
    /**
     * Private helper functions for routes
     * Validators, getters and setters
     */

    /**
     * Retrieve Controller string or false if does not exist
     *
     * @param string $param - uri route
     * @return mixed       - returns the string name of the requested controller if it exists, false otherwise
     */
    private static function getController($param)
    {

        // if not root, check for preg_match and first returned match is controller name
        if ($param != '/') {

            // run preg_match and get first match group
            preg_match('/^([a-zA-Z0-9]+)[^\/]?/', $param, $match);

            // get namespace from _namespace array
            $namespace = self::$_namespace[$match[0]];

            // set controller, if namespace is null, return from _controllers, otherwise, append $namespace string
            $controller = is_null($namespace) ? self::$_controllers[$match[0]] : $namespace . '\\' . self::$_controllers[$match[0]];
        } else {

            // same as above, edge case for root
            $namespace = self::$_namespace[$param];

            // this should never be necessary... so we'll probably remove it
            $controller = is_null($namespace) ? self::$_controllers[$param] : $namespace . '\\' . self::$_controllers[$param];
        }

        // return the controller if it exists, or return false if not
        return class_exists("Controller\\" . $controller) ? "Controller\\" . $controller : false;
    }

    // get method from param, verb and class
    private static function getMethod($param, $verb, $class)
    {
        // define method from _method array w/ param input and verb
        $method = self::$_method[$param][$verb];

        // return method or false
        return method_exists($class, $method) ? $method : false;
    }

    private static function formatProp($prop)
    {
        $prop = trim(trim($prop, '{'), '}');

        if (preg_match('::', $prop)) {
            $prop = explode('::', $prop)[0];
        }

        return $prop;
    }

    // test if value is a variable within the URI string
    private static function isVar($value)
    {
        return preg_match('/(\{(.*)\})/', $value);
    }

    private static function isType($value)
    {
        // currently just checking basic 0x format for Uids
        /**
         * TODO: split uri vars for formats, ie {id:\d} to handle non-default values
         */
        // return preg_match('/^[0][x][\w-]+$/', $value);

        // temporarily just check numeric
        if (preg_match('/[0-9]+/', $value)) {
            return true;
        }

        return false;
    }

    /**
     * Create the holder arrays for Controller/Methods/URIs
     */

    private static function createController($uri, $controller)
    {
        $uriRoot = explode('/', $uri)[0];
        try {
            if (preg_match('/([A-Z][a-z]+([A-Z][a-z]+)+?)/', $controller)) {
                if (!self::$_controllers[$uriRoot] && self::$_controllers[$uriRoot] != $controller) {
                    self::$_controllers[$uriRoot] = $controller;
                    return true;
                } else {
                    // return false;
                    // already exists and/or is already set equal to controller
                    // need a bounce here, but maybe just an activity log
                    // ? this else call may be redundant, although may be relevant as we write this out
                }
            } else {
                throw new \Exception('<h2>Could not create CONTROLLER: ' . $controller . ' in <u>' . __FUNCTION__ . '</u></h2><p>CONTROLLER must be Pascal case, ex: TestController.</p>');
            }
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }
    private static function createUri($uri)
    {
        try {
            if (preg_match('/^[a-z][a-z\/\{\}:]+$/', $uri) || $uri == '/') {
                if (!in_array($uri, self::$_uri)) {
                    self::$_uri[] = $uri;
                }
            } else {
                throw new \Exception('<h2>Could not create URI: [' . $uri . '] in <u>' . __FUNCTION__ . '</u></h2><p>URI must contain only letters and forward slashes. No numbers or special characters.</p>');
            }
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }
    private static function createMethod($uri, $method, $verb)
    {
        try {
            if (preg_match('/^[a-z]+([A-Z][a-z]{0,})*?$/', $method)) {
                if (!self::$_method[$uri][$verb] && self::$_method[$uri][$verb] != $method) {
                    self::$_method[$uri][$verb] = $method;
                }
            } else {
                throw new \Exception('Could not create METHOD: ' . $method . ' in <u>' . __FUNCTION__ . '</u></h2><p>METHOD must be camel case, ex: testMethod()</p>');
            }
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }
    public function dump()
    {

        print_r(self::$_uri);
        print_r(self::$_controllers);
        print_r(self::$_method);
    }

    public static function overrideExistingRoute($uri, $controller, $method, $verb = 'GET')
    {
        self::$_controllers[$uri] = $controller;
        self::$_method[$uri][$verb] = $method;
    }
}