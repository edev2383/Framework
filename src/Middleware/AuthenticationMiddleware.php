<?php

namespace Middleware;

use Edev\Model\Employee;
use Edev\Model\EmployeePermissions;
use Edev\Model\Permission\AccessCore;
use Edev\Model\Permission\AccessDetail;
use Edev\Model\User;

class AuthenticationMiddleware // implements AuthInterface

{

    public function __construct()
    {
        //

    }

    public function getSession()
    {
        //
        $_SESSION['first_name'] = ucfirst($_SESSION['first_name']);
        $_SESSION['last_name'] = ucfirst($_SESSION['last_name']);
        $userData = $_SESSION;
        unset($userData['response']);
        return $userData;
    }

    public function getNewSession()
    {
        return $_SESSION['user'] ?? [];
    }

    public function destroySession()
    {
        session_destroy();
        unset($_SESSION);
    }

    public function getResponse()
    {
        // session_start();
        return $_SESSION['response'] ?? [];
    }

    private function JSONserialize($string)
    {
        return json_encode($string);
    }

    /**
     *
     * @param object $jsonObject
     * @param bool $array
     */
    private function JSONparse($jsonObject, $array = true)
    {
        return json_decode($jsonObject, $array);
    }

    /**
     *
     * @param string $cookieName
     * @return mixed (string/bool:false)
     */
    public function getCookie($cookieName)
    {
        // check if exists
        if (isset($_COOKIE[$cookieName])) {

            // alias and return parsed
            $cookie = $_COOKIE[$cookieName];
            return $this->JSONparse($cookie, true);
        }

        // return false
        return;
    }
    public function clearPinCookie($cookieName)
    {
        setcookie($cookieName, '', time() - 3600, '/', '.zerodock.com', false, false);
    }

    public function logout()
    {

        session_destroy();
    }

    public function getGuestUser()
    {
        return $this->_createGuestUser();
    }
    private function _createGuestUser()
    {
        return [
            // 'employeeId' => 111111,
            'loggedIn' => 0,
            'userId' => 111111,
            // 'status' => 0,
            // 'email' => null,
            // 'firstName' => 'GUEST',
            // 'lastName' => '',
            // 'nickname' => 'GUEST',
            // 'permissions' => $this->getEmployeePermissionsByEmployeeId(111111),
        ];
    }
    private function getUserDataByUserId($id)
    {
        return User::where('id', $id)->get('id', 'email');
    }

    private function getEmployeeDataByUserId($user_id, $status = 1)
    {
        return Employee::where('user_id', $user_id)
            ->andWhere('status', $status)
            ->get();
    }

    public function getEmployeePermissionsByEmployeeId($employee_id)
    {

        $permission_id = EmployeePermissions::where('employee_id', $employee_id)->get('permission_id');
        $permissions = AccessDetail::where('permission_id', $permission_id)->get('access_id', 'value');

        $remapPermissions = [];
        foreach ($permissions as $k => $v) {
            extract($v);
            $id = $access_id;
            $shortName = AccessCore::where('id', $id)->get('permission_short_name');
            $remapPermissions[$shortName] = $value;
        }

        return $remapPermissions;
    }
    public function loginUserByUserId($id)
    {

        // get user details & extract values
        $user = \Edev\Model\User::where('id', $id)->andWhere('status', 1)->get('id as user_id', 'email');
        extract($user);

        // get employee details & extract values
        $employee = \Edev\Model\EmployeeView::where('user_id', $user_id)->get('id as emp_id', 'status', 'first_name', 'last_name', 'nickname');
        extract($employee);

        // create session[user]
        $_SESSION['user'] = [
            // 'employeeId' => $emp_id,
            'loggedIn' => 1,
            // 'testing' => 'a fine',
            'userId' => $user_id,
            // 'status' => $status,
            // 'email' => $email,
            // 'firstName' => $first_name,
            // 'lastName' => $last_name,
            // 'nickname' => $nickname,
            // 'permissions' => $this->getEmployeePermissionsByEmployeeId($emp_id),
        ];

        session_write_close();

        // $this->saveSessionAsCookie();
    }

    public function saveSessionAsCookie()
    {
        setcookie('pa_session', json_encode($_SESSION), time() + 36000, '/', 'dev.zerodock.com', false, false);
    }
}