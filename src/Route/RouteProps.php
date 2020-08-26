<?php

namespace Edev\Route;

/**
 * Parse the inline props for the route
 * TODO: Combine data and props into one object, auto parsing the JSON out into
 * TODO: top level props
 */
class RouteProps
{

    private $_route;
    private $_match;
    private $_reIdInt = '/\{(.*?)\}/';
    private $_props = [];

    public function __construct(string $route, string $match)
    {
        [$this->_route, $this->_match] = $this->_trimLikeValues($this->_breakString($route), $this->_breakString($match));

        $this->_setProps();
    }

    /**
     * Set _prop array that is passed up to the Controller
     *
     * @return void
     */
    private function _setProps()
    {
        $this->_props = $this->_cleanPropNames(array_combine($this->_match, $this->_route));
    }

    /**
     * Helper method to explode the incoming route/match strings
     *
     * @param string $string
     * @return void
     */
    private function _breakString(string $string): array
    {
        return explode('/', $string);
    }

    /**
     * Unset like values from the array. If the values match, then they are part
     * of the concrete route and not relevant to the props
     *
     * @param array $routeArr
     * @param array $routeMatch
     * @return void
     */
    private function _trimLikeValues(array $routeArr, array $routeMatch)
    {
        $len = count($routeArr);
        for ($ii = 0; $ii < $len; $ii++) {
            if ($routeArr[$ii] == $routeMatch[$ii]) {
                unset($routeArr[$ii]);
                unset($routeMatch[$ii]);
            }
        }
        return [array_values($routeArr), array_values($routeMatch)];
    }

    /**
     * Remove the curly brackets from the prop names
     *
     * @param array $props
     * @return void
     */
    private function _cleanPropNames(array $props)
    {
        $c = [];
        foreach ($props as $key => $val) {
            $c[$this->_cleanPropName($key)] = $val;
        }
        return $c;
    }

    /**
     * Performs the actual cleaning of the prop name
     *
     * @param string $propName
     * @return void
     */
    private function _cleanPropName(string $propName)
    {
        preg_match($this->_reIdInt, $propName, $m);
        return strpos($m[1], ':') !== false ? explode(':', $m[1])[0] : $m[1];
    }

    /**
     * getter method to get the props array and pass it up the line to controller
     *
     * @return void
     */
    public function getProps()
    {
        return $this->_props;
    }
}
