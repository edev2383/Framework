<?php

namespace Edev\Controller;

class TemplateController extends \Edev\Controller\Controller
{

    private $_method;

    protected $_data = [];

    // private $_class;

    const _TABLE = 'employees';

    public function __construct()
    {
    }

    public function index()
    {
        // * route: /template [GET]

    }

    /**
     *  store new user to the db
     *  /user [POST]
     */
    public function store()
    {
        // * route: /template [POST]

        // echo __FUNCTION__;
        // print_r($data);

        // $this->render('user.index');
    }

    /**
     *  The page for creating new users
     */
    public function create()
    {
        // * route: /template/create
    }

    public function show()
    {
        // * route: /template/{id} [GET]

    }

    public function update()
    {
        // * route: /template/{id} [PUT]
        // echo __CLASS__;
        // echo '<br />';
        // echo __FUNCTION__;
    }
    public function edit()
    {
        // * route: /template/{id}/edit

    }

    public function destroy()
    {
        // * route: /template/{id} [DELETE]

    }
}
