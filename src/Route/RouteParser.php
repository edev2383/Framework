<?php

namespace Edev\Route;

use Ifs\ParserInterface as ParserInterface;
use Edev\Route\Data\Parse;

class RouteParser implements ParserInterface
{

    private $_slug;

    private $_uri;

    protected $_data;

    const DEFAULT_REGEX = '/[^\/]+/';

    private $_prefix = '';

    public function __construct()
    {
        //

    }

    public function init()
    {
        $this->_slug = $this->_data;
        $this->_uri = array_shift($this->_slug);

    }

    public function capture($method)
    {
        try {
            $data = file_get_contents('php://input');
            $this->_method = $method;
            switch ($method) {
                case 'GET':
                    $this->_data = $_GET;
                    $this->init();
                    break;
                case 'POST':
                    $this->_data = Parse::parse($method, $data);
                    $this->_uri = str_replace($this->_prefix, '', $_SERVER['REQUEST_URI']);
                    break;
                case 'PUT':
                    $this->_uri = str_replace($this->_prefix, '', $_SERVER['REQUEST_URI']);
                    $this->_data = Parse::parse($method, $data);
                    break;
                case 'DELETE':
                    $this->_uri = str_replace($this->_prefix, '', $_SERVER['REQUEST_URI']);
                    $this->_data = Parse::parse($method, $data);
                    break;
                default:
                    $this->_data = $_GET;
            }

            if (is_null($this->_uri)) {
                // throw new \Exception('URI empty. Something went wrong.');
            }
        } catch (\Exception $e) {
            die($e->getMessage());
        }

        // print_r($this);
        // return $this->_data;
    }
    public function parseSlug()
    {
    }

    public function parseUri()
    {
        // echo $this->uri;

        // echo $this->uri;

        // preg_match_all('/[^\/]+/', $this->uri, $this->m);
        // print_r($this->$m);
    }

    public function returnUri()
    {
        $this->_uri = ($this->_uri == false || $this->_uri == 'index.php') ? '/' : trim($this->_uri, '/');
        return $this->_uri;
    }

    public function returnParse()
    {
        // var_dump($this);
        // testing;
        // print_r($this);
        return $this->_data;
    }

    public function setPrefix($string)
    {
        $this->_prefix = $string;
    }
}
