<?php

namespace Edev\Controller;

use Exception;
use Middleware\AuthenticationMiddleware as Authenticate;
use Edev\Model\MetaClient;
use Edev\Resource\Display as Display;
use Edev\Resource\FlashAlert as FlashAlert;
use Edev\Resource\Status as Status;
use Edev\System\Helpers\Arr;
use Edev\System\Helpers\Domain;
use \PDO;
use Edev\Model\Meta\ClientGlobal;

class Controller implements \Ifs\ControllerInterface
{

    // _view defines the default folder path for views
    protected $_viewPath = 'View/';
    protected $_layout = 'layout';
    protected $_data = [];
    protected $pdoName = 'client';
    protected $_newData = [];

    protected $logger;

    public function __construct()
    {
    }

    public function bindRepos()
    {
    }

    public function init()
    {
        // $this->connect();
        $this->_init();
        $this->bindRepos();
        $this->_extendInit();
    }

    protected function _extendInit()
    {
    }

    protected function _getRoot()
    {
        return explode('/', trim($_SERVER['REQUEST_URI'], "/"))[0];
    }

    public function __destruct()
    {
        // \Edev\Database\Container::getInstance()->clearAll();
        unset($this->db->pdo);
        unset($this->db);
    }

    private function captureRequestedRoute()
    {
        // reset session value
        $requestUri = $_SERVER['SCRIPT_URL'];
        if ($requestUri != '/account/login') {
            if (!isset($_SESSION['req'])) {
                $_SESSION['req'] = $requestUri;
            } else {
            }
        }
    }

    /**
     * Deprecated - Marked for deletion
     *
     * @return void
     */
    public function getConnection()
    {
        return \Edev\Database\Container::getInstance()
            ->getConnectionByName('default');
    }

    /**
     *  Initialize any necessary variables within the controller
     *  Called in child __construct()
     */
    public function _init()
    {

        $this->setRandProp();

        /**
         * Status outputs JSON object to the Core.js - CoreLIb.prototype
         * It standardizes how these interact and allows for response and reject
         * based on the response in a highly structured way
         */
        $this->setStatusHandler();

        $this->authenticate();

        // $this->setResponse();
    }

    protected function _getPdo($name)
    {
        return $this->getConnection();
    }

    /**
     *
     */
    protected function setRandProp()
    {
        $this->setDisplayAttribute('rand', rand(0, 200000));
    }



    /**
     *
     */
    protected function setStatusHandler()
    {
        $this->status = new Status();
    }


    /**
     *
     */
    public function authenticate()
    {
        if (!isset($this->auth)) {
            // $this->auth = new Authenticate($this->getClientPdo());
        }
        $this->setUser();
    }

    /**
     * Set top level user property
     */
    protected function setUser()
    {
        $this->user = \Edev\Resource\User\User::getInstance();
    }

    /**
     *
     */
    protected function getUserDisplayData()
    {
        $this->_newData = array_merge($this->_newData, $this->user->getDisplayProps());
    }

    /**
     *
     */
    protected function setDisplayAttribute($id, $value)
    {
        $this->_newData[$id] = $value;
    }

    /**
     *
     */
    protected function setAttribute($id, $value)
    {
        $this->{$id} = $value;
    }

    /**
     *
     */
    public function archive_render($filePath, $data = [])
    {
        // create new Display objects
        // From base controller, call child class name and strip out Controller to get root dir
        if (empty($data)) {
            $data = $this->_newData;
        } else {
            $data = array_merge($data, $this->_newData);
        }

        // define file path
        $this->display = new Display($this->_viewPath . $filePath, $data);

        new \Edev\Resource\DevBanner();

        return $this;
    }

    public function new_render($filePath, $data = [])
    {
        // create new Display objects
        // From base controller, call child class name and strip out Controller to get root dir
        if (empty($data)) {
            $data = $this->_newData;
        } else {
            foreach ($data as $k => $v) {
                $this->_newData[$k] = $v;
            }
            $data = $this->_newData;
        }

        // define file path
        $this->display = new \Edev\Resource\NewDisplay($this->_viewPath . $filePath, $data);

        return $this;
    }

    public function render($filePath, $data = [], $layout = null)
    {

        if (gettype($data) == 'string') {
            $layout = $data;
            $data = [];
        }
        // create new Display objects
        // From base controller, call child class name and strip out Controller to get root dir
        if (empty($data)) {
            $data = $this->_newData;
        } else {
            foreach ($data as $k => $v) {
                $this->_newData[$k] = $v;
            }
            $data = $this->_newData;
        }

        $layout = $layout ?: $this->_layout;

        // define file path
        $display = new \Edev\Resource\Display\DisplayVersionTwo();
        $display->setLayout($layout);
        // $display->setLayout('altLayout');
        $display->setFilePath($this->_viewPath . $filePath);
        $display->setData($data);
        $display->render();

        // new \Edev\Resource\DevBanner();

        return $this;
    }

    /**
     * execute a custom query
     *
     * @param string $query
     * @param array $data-
     *
     * @return bool status
     */
    public function execute($query, $data = null)
    {
        mail('jeff@jasoncases.com', 'Edev - Deprecation Warning', json_encode(debug_backtrace()));
        die('<h2> Controller::execute() has been deprecated.
            Please use/create the appropriate \Model class');
    }

    /**
     * Maps to Database_Handler::get()
     *
     * Chained method for SQL queries
     *
     * @param mixed $target - can be an array, or multiple strings, ::get('one', 'two', 'three', 'four') or ::get(['five', 'six', 'seven']);
     *
     * @return object $this->db
     */
    public function get()
    {
        mail('jeff@jasoncases.com', 'Edev - Deprecation Warning', json_encode(debug_backtrace()));
        die('<h2> Controller::get() has been deprecated.
            Please use/create the appropriate \Model class');
    }
    /**
     * Main setter method for all children
     *
     * @param string $action
     * @param array $arrayValues
     * @param string $table [Defaults to $this::_TABLE]
     *
     * @return bool
     */
    public function set($action, $arrayValues, $table = null)
    {
        mail('jeff@jasoncases.com', 'Edev - Deprecation Warning', json_encode(debug_backtrace()));
        die('<h2> Controller::set() has been deprecated.
            Please use/create the appropriate \Model class');
    }

    /**
     * delete a record from specified table w/ specified id
     *
     * @param int $id the idea of the record to delete
     * @param string $table the table from which to remove the record
     *
     * @return bool success or failure of the execute command
     */
    public function delete($id, $table = null)
    {
        mail('jeff@jasoncases.com', 'Edev - Deprecation Warning', json_encode(debug_backtrace()));
        die('<h2> Controller::delete() has been deprecated.
            Please use/create the appropriate \Model class');
    }

    protected function _isDev()
    {
        $wl = ['dev', 'feature'];
        $reqURI = $_SERVER['SERVER_NAME'];
        $reqRoot = explode('.', $reqURI)[0];
        return in_array($reqRoot, $wl);
    }

    /**
     * redirect
     *
     * @param string $url url for redirect, defaults to global 'path_to_root'
     * @return void
     */
    public function redirect($url = null)
    {
        header('Location: ' . $url ?: '/');
    }

    protected function getUserPermissionValue($permission_short_name)
    {
        return $this->user->getPermission($permission_short_name);
    }
    /**
     *
     *
     * @param string $permission_short_name
     *
     * @return void
     */
    public function checkPermissionAccess($permission_short_name)
    {
        // // get access_id from permission short name

        // return set permission value
        $value = $this->getUserPermissionValue($permission_short_name);

        // echo $value . '<hr />';
        // die('testing here');
        // if value is false/0
        if (!$value) {

            // set message
            $this->message(
                'Permission check failed. Please see an administrator',
                'error'
            );

            // redirect
            $this->redirect();
        }
    }

    /**
     * differs from checkPermissioAccess in that it returns a boolean,
     * rather than forcing a redirect. Use this when a redirect/distinct
     * view isn't necessary
     *
     * @param string permission_short_name
     * @return bool
     */
    public function routePermissionAccess($permission_short_name)
    {
        return $this->getUserPermissionValue($permission_short_name);
    }

    /**
     *
     * @param string $message
     * @param string $status 'success' or 'error'
     *
     * @return \Edev\Resource\FlashAlert
     */
    public function message($message, $status = 'success')
    {
        $allowed = ['success', 'error']; 
        if (!in_array($status, $allowed)) {
            throw new \Exception\IllegalValueException('Incorrect status value entered. Must be "success" or "error".');
        }
        return new \Edev\Resource\FlashAlert($message, $status);
    }

    /**
     * submitNewActionToAccessLog
     *
     * @param int $id
     * @param mixed $additionalData this value gets encoded and saved in the _data field, helpful for debugging and action tracking
     * @return void
     */
    public function submitNewActionToAccessLog($id = null, $additionalData = null)
    {
        // method to log user id, requested URI, referal URI, and time created
        // hard code all requested items because I can't imagine a scenario where I'd have to change to log parameters

        $uriBlacklist = [
            '/response',
            '/response/clear',
            '/user/status',
        ];

        // debugging for access issue
        if ($id != 1 && $id != 14) {
            // check that last_unix_ts is set
            if (isset($additionalData['last_unix_ts'])) {
                // and that no more than 15 seconds has passed
                if (time() - $additionalData['last_unix_ts'] > 15) {
                    //
                    $uriBlacklist[] = '/user/status';
                }
            }
        }

        $newAccessLogSubmition = [];
        $newAccessLogSubmition['employee_id'] = is_null($this->id) ? $this->_GUEST : $this->id;
        $newAccessLogSubmition['referral_uri'] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'none/self/refresh';
        $newAccessLogSubmition['request_method'] = $_SERVER['REQUEST_METHOD'];
        $newAccessLogSubmition['access_uri'] = $_SERVER['REQUEST_URI'];
        $newAccessLogSubmition['_data'] = json_encode($additionalData);

        // TODO DEVELOP BLACKLIST OF URIs TO IGNORE
        if (!in_array($_SERVER['REQUEST_URI'], $uriBlacklist)) {
            // if($this->id != 14) {  // hardcoding developer ID to allow for more natural access log flow
            try {
                if (\Edev\Model\AccessLog::save($newAccessLogSubmition)) {
                    throw new Exception('Access log creation failed.');
                }
            } catch (Exception $e) {
                $this->message($e->getMessage(), 'error');
            }
            // }
        }
    }

    /**
     * check for user login status, redirects to the root if not
     *
     * @return void
     */
    public function isUserLoggedIn()
    {
        if (!$this->user->isLoggedIn()) {
            $this->message('Access denied. Please log in.', 'error');
            extract(ClientGlobal::get('path_to_root'));
            die(header('Location: ' . $path_to_root));
        }
    }

    /**
     * checks incoming data against acceptable whitelist, defined by an array at the start of the method
     * throws an error when there is a data mismatch to protect against some injected values
     *
     * @param array $incomingData HTTPS acquired data
     * @param array $whitelistData acceptable data defined in the method
     * @return bool returns true if the data passes, dies with error message if not
     */
    public function compareInputData($incomingData, $whitelistData)
    {
        try {
            $incomingKeys = array_keys($incomingData);
            foreach ($incomingKeys as $value) {
                if (!in_array($value, $whitelistData)) {
                    throw new Exception\IllegalValueException('Unexpected value present: ' . $value . '. Please try again.');
                }
            }
            return true;
        } catch (Exception\IllegalValueException $e) {
            $this->message($e->getMessage(), 'error');
            die();
        }
    }

    /**
     * Shortens a string to desired length, takes into account full word breaks
     *
     * @param string $str string
     * @param int $len length
     *
     * @return string
     */
    public function truncate($str, $len)
    {
        $strArr = explode(' ', $str);
        $lenCount = 0;
        $strContainer = [];
        $ii = 0;
        while ($lenCount <= $len) {
            // no more, break loop
            if (!isset($strArr[$ii])) {
                break;
            }

            $lenCount += strlen($strArr[$ii]);
            $strContainer[] = $strArr[$ii];
            $ii++;
        }
        return implode(' ', $strContainer);
    }

    /**
     * Check incoming data for inconsistencies, ensure only required field/var names are being pushed to methods
     *
     * @param mixed $args Initial para
     */
    protected function cleanse($args)
    {
        $keys = array_keys($args);
        foreach ($args as $key => $value) {
        }
    }

    /**
     * Binds repository to top level Controller::prop via pre-defined Repository::root value, becomes $this->[root]
     *
     * @param object $repository
     */
    public function bind($repository)
    {
        // phpCore try catch block
        try {
            if (isset($this->{$repository->root})) {

                // code block ....
                throw new \Exception('Repository->root overwritten. Please instantiate the second repository and change "root" value before binding.');
            }

            $this->{$repository->root} = $repository;
            //$this->message('');
        } catch (\Exception $e) {

            $this->outputLog();
            die($e->getMessage());
        }
    }


    private function outputLog()
    {
        $backtrace = debug_backtrace();
        $caller = $backtrace[1];
        echo '<h2>Error:</h2> <h3>File: ' . $caller['file'] . '::(' . $caller['line'] . ')</h3>';
    }

    public function setData(array $data)
    {
        if (key_exists('props', $data)) {
            extract($data);
            $this->_registerProps($props);
            // unset($data['props']);
        }

        $this->_data = $data;
    }

    public function setProps(array $props = [])
    {
        $this->_registerProps($props);
    }
    private function _registerProps($props = [])
    {
        if (!empty($props)) {
            $this->props = new \Edev\Resource\Props($props);
        }
    }
}
