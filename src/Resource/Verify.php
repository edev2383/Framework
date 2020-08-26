<?php

namespace Edev\Resource;

use Edev\Model\Meta\UserVerification;

class Verify
{

    private $_table = 'meta_user_verification';
    private $_expiration = 600; // 10 minutes

    public function __construct()
    {
        $this->token = $this->_getToken();
    }

    private function _getToken()
    {
        return Token::create();
    }

    private function _serialize(array $userData)
    {
        return json_encode($userData);
    }

    public function store(array $userData)
    {
        $token = $this->token;
        if (!UserVerification::save($this->_formatValues(array_merge($userData, compact('token'))))) {
            throw new \Exception\DatabaseOperationException('Error creation verification routine.');
        }
        $token = $this->token;
        return $this->_sendVerificationEmail(array_merge($userData, compact('token')));
    }

    private function _formatValues(array $userData)
    {
        return [
            'user_data' => $this->_serialize($userData),
            'token' => $this->token,
            'unix_ts' => time(),
        ];
    }

    public function verify(string $token)
    {
        $this->_setVerifiedByToken($token);
    }

    private function _setVerifiedByToken(string $token)
    {
        $this->v = $this->_getVerificationByToken($token);

        if ($this->registrationCompleted()) {
            throw new \Exception\DatabaseOperationException('Account already registered.');
        } else {
            if (!$this->isVerified()) {
                if (!$this->isExpired()) {
                    return $this->_setVerifiedStateById($this->v['id']);
                } else {
                    throw new \Exception\DatabaseOperationException('Token Expired.');
                }
            }
        }
    }

    private function _getVerificationByToken(string $token)
    {
        return UserVerification::where('token', $token)->get();
    }

    public function isVerified()
    {
        return $this->v['verified'];
    }

    public function isExpired()
    {
        return time() - $this->v['unix_ts'] > $this->_expiration;
    }

    public function registrationCompleted()
    {
        return $this->v['registration_complete'];
    }
    public function getJSONUserData()
    {
        return $this->v['user_data'];
    }

    private function _setVerifiedStateById(int $id)
    {
        $verified = 1;
        return UserVerification::update(compact('id', 'verified'));
    }

    private function _sendVerificationEmail(array $userData)
    {
        extract($userData);
        $message = $this->_template($this->_getTemplate(), $userData);
        // $email = 'jeff@jasoncases.com';
        return Email::to($email)->subject('Edev: New User Verification')->message($message)->compose();
    }

    private function _getTemplate()
    {
        return EmailTemplate::load('verifyuser');
    }

    private function _template(string $email, array $userData)
    {
        return Templater::parse($email, $userData);
    }
    public function setRegistered($token)
    {
        $id = $this->_getVerificationByToken($token)['id'];

        return $this->_setRegisteredById($id);
    }
    private function _setRegisteredById($id)
    {
        $registration_complete = 1;
        return UserVerification::update(compact('id', 'registration_complete'));
    }
}
