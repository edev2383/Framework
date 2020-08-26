<?php

namespace Edev\Controller;

use Edev\Model\Meta\ClientGlobal;
use Edev\Model\MockModel;
use Edev\System\Helpers\Arr;
use Edev\Model\Test;
use Edev\Resource\Py\Py;
use Edev\Resource\Shell;

class HomeController extends \Edev\Controller\Controller
{
    protected $_viewPath = 'View/';
    const _TABLE = '';

    public function __construct()
    {
    }

    public function index()
    {
       $path = './cgi-bin/py/scraper/';
       $file = 'test.py';

       $response = Py::build('scraper', $file)->run();

        $x = json_decode($response);
        Arr::pre($x);
    }

    public function getUserPage()
    {

        if ($this->user->isLoggedIn()) {
            $this->render('user_page.html');
        } else {
            $this->render('login.html');
        }
    }
    public function getResponse()
    {
        if ($this->response != null) {
            $this->status->aux(json_decode($this->response))->echo();
        } else {
            $this->status->error();
        }
        $this->getClearResponse();
    }
    public function getClearResponse()
    {
        $this->response = '';
        unset($_SESSION['response']);
    }
    public function getLogin()
    {

        if ($this->user->isLoggedIn()) {
            return $this->render('user_page.html');
        }

        $loginWithEmail = isset($_GET['loginWithEmail']) ? $_GET['loginWithEmail'] : false;

        $this->_newData['root'] = '/';

        if (!$loginWithEmail) {
            // render pinpad
            $this->render('pinpad.html');
        } else {
            // render login
            $this->render('login.html');
        }
    }

    public function getBadge()
    {
        $incomingData = json_decode($this->_data['data'], true);
        extract($incomingData);
        $color = $color ? $color : 'black';
        $fontSize = $fontsize ? $fontSize : '14px';
        try {
            $badge = $this->badge->output($name, $color, $fontSize);
            $this->status->aux($badge)->echo(); 
        } catch (\PDOException $e) {
            $this->message($e->getMessage(), 'error');
            $this->status->error();
        }
    }

  
    public function getLogout()
    {

        $this->submitNewActionToAccessLog();

        $userHandle = new \Edev\Resource\UserHandler();
        $userHandle->logout();

        header('Location: /landing');
    }

    public function getAllRoutes()
    {
        header('Location: /?dxr');
    }

    public function getGlobalVar()
    {
        $global['constant'] = $this->_data['constant'];

        // $result[0][value]
        $result = current(ClientGlobal::get($this->props->constant));

        if (!empty($result)) {
            $global['value'] = $result[0]['value'];
        } else {
            $global['value'] = 'undefined';
        }

        echo json_encode($global);
    }

    public function getSession()
    {
        echo json_encode($_SESSION, JSON_PRETTY_PRINT);
    }

    public function postSession() {
        $_SESSION[$this->props->key] = $this->props->data;
    }

    /**
     * Returns client global settings
     *
     * @return void
     */
    public function getClient()
    {
        $clientSideGlobals = \Edev\Model\Meta\ClientGlobal::all('constant', 'value');
        $this->status->aux($clientSideGlobals)->echo();
    }
}
