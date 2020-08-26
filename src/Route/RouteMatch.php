<?php

namespace Edev\Route;

use Edev\System\Helpers\Arr;

class RouteMatch
{
    private $_matches = [];
    private $_variableRoutes = [];
    private $_route;
    private $_props = [];

    private $_reIdInt = '/\{(.*?)\}/';
    private $_reOther = '/\{(.*?):(.*?)\}/';
    private $_reInt = '/^[0-9]+$/';

    public function __construct(array $variableRoutes, string $route)
    {
        $this->_variableRoutes = $variableRoutes;
        $this->_route = $route;
    }

    /**
     * Returns the matching route form the raw match array sent and sets the prop object
     *
     * @return void
     */
    public function getRouteMatch()
    {

        $this->_matches = array_merge($this->_matches, $this->_roughMatchVariableRoutesAgainstIncomingRoute());

        // if (count($this->_matches) == 1) {

        //     $this->_setProps(current($this->_matches));
        //     return current($this->_matches);
        // } else {
        // if there are more than one after _roughMatch..(), compare against variable types
        /**
         * Remove the value check because it was allowing non-matching
         * prop types through and serving the wrong route. I inaccurately
         * assumed if there was a single match, it would be the right
         * route, but timesheets/tests was serving up the show method
         * when it should have been invalid
         * Now all incoming routes are type checked.
         */
        return $this->_compareVariableTypes();
        // }
    }

    /**
     * getter method to send props upstream
     *
     * @return void
     */
    public function getProps()
    {
        return $this->_props->getProps();
    }

    /**
     * Compares length of route segments, split at '/'
     *
     * @return void
     */
    private function _roughMatchVariableRoutesAgainstIncomingRoute(): array
    {
        $c = [];
        foreach ($this->_variableRoutes as $val) {
            if (count(explode('/', $this->_route)) == count(explode('/', $val))) {
                $c[] = $val;
            }
        }
        return $c;
    }

    /**
     * If more than one match exists after _roughMatch, we need to compare against
     * the variable types.
     *  Example:
     *      Matches: 1.) /route/1/edit
     *               2.) /route/name/load
     *
     * @return void
     */
    private function _compareVariableTypes()
    {
        foreach ($this->_matches as $val) {
            if (isset($_GET['rida'])) {
                echo 'GETROUTEMATCH::::::';
                Arr::pre($val);
            }
            [$propValue, $rawProp] = $this->_trim($this->_route, $val);
            if ($this->_compareDirectTypes($propValue, $rawProp)) {
                $this->_setProps($val);
                return $val;
            }
        }
    }

    /**
     * Unset like values from the array. If the values match, then they are part
     * of the concrete route and not relevant to the props
     *
     * @param array $routeArr
     * @param array $routeMatch
     * @return void
     */
    private function _trim(string $route, string $match)
    {
        $routeArr = explode('/', $route);
        $routeMatch = explode('/', $match);
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
     * direct comparison of two arrays, after _trim method
     *
     * @param array $rawValues
     * @param array $rawProps
     * @return void
     */
    private function _compareDirectTypes(array $rawValues, array $rawProps)
    {
        $len = count($rawValues);
        for ($ii = 0; $ii < $len; $ii++) {
            $currVal = $rawValues[$ii];
            $currProp = $rawProps[$ii];
            if (isset($_GET['rida'])) {
                echo "[ currVal: $currVal ] [ currProp: $currProp ]";
                $type = $this->_getPropValueType($currVal);
                $raw = $this->_getRawPropType($currProp);
                echo "[ type: $type ] [ raw: $raw ]";
            }

            if ($this->_getPropValueType($currVal) != $this->_getRawPropType($currProp)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Create the _props pointer to the RouteProps object
     *
     * @param string $match
     * @return void
     */
    private function _setProps(string $match)
    {
        $this->_props = new \Edev\Route\RouteProps($this->_route, $match);
    }

    /**
     * Simple type checking for the variables in the routes/matches
     *
     * @param string $propValue
     * @return void
     */
    private function _getPropValueType(string $propValue)
    {
        return preg_match($this->_reInt, $propValue) ? 'int' : 'string';
    }

    /**
     * Simple type checking for the variables in the routes/matches
     *
     * @param string $rawProp
     * @return void
     */
    private function _getRawPropType(string $rawProp)
    {
        if (preg_match($this->_reOther, $rawProp, $m)) {
            return $m[2];
        }
        return 'int';
    }
}
