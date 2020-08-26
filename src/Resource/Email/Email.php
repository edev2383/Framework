<?php

namespace Edev\Resource;

use PHPMailer\PHPMailer\PHPMailer;
use Edev\Model\Employee;
use Edev\System\Helpers\Arr;

class Email
{
    public $replyFrom = 'no-reply@zerodock.com';
    private $_defaultTemp = 'jeff@jasoncases.com';
    private $isset_ReplyFrom = false;

    private $re = '/(.*?)@(.*?),/';

    protected $mail;

    public function __construct()
    {
        // set mail to the PHPMailer library class
        $this->mail = new PHPMailer(true);
    }

    /**
     * Initial creation method, allows for Email::to()->
     * without having to explicitly instantiate
     *
     * @param mixed $email - can be string or array of strings
     * @return \Email
     */
    public static function to($email)
    {
        return (new static)->_to($email);
    }

    /**
     * Begin handling the PHPmailer() methods, adds recipients
     *
     * @param mixed $email
     * @return \Email
     */
    private function _to($email)
    {

        // ensure $email is the proper shape so as to not cause errors
        $email = $this->_formatEmail($email);

        // if $email is an array, loop and add each email
        if (is_array($email) || is_object($email)) {
            foreach ($email as $e) {
                $this->mail->addAddress($e);
            }
        } else {

            // if it's a string, add the address
            $this->mail->addAddress($email);
        }

        return $this;
    }

    /**
     * Sets from and replyTo values
     *
     * @param mixed $email
     * @return \Email
     */
    public function from($email = 'no-reply@zerodock.com', $name = 'Edev')
    {

        $this->mail->setFrom($email, $name);
        $this->setReplyTo($email, $name);
        return $this;
    }

    /**
     *
     * @param mixed $email
     * @return \Email
     */
    public function subject($subject)
    {
        $this->mail->Subject = $subject;
        return $this;
    }

    /**
     *
     * @param mixed $email
     * @return \Email
     */
    public function addCC($email = null)
    {

        if (is_null($email)) {
            return $this;
        }
        $email = $this->_formatEmail($email);
        if (is_array($email) || is_object($email)) {
            foreach ($email as $e) {
                $this->mail->addCC($e);
            }
        } else {
            $this->mail->addCC($email);
        }
        return $this;
    }

    /**
     *
     * @param mixed $email
     * @return \Email
     */
    public function noReply()
    {
        $this->from($this->replyFrom);
        return $this;
    }

    /**
     *
     * @param mixed $email
     * @return \Email
     */
    public function setReplyTo($email, $name)
    {
        $this->isset_ReplyFrom = true;
        $this->mail->addReplyTo($email, $name);
        return $this;
    }

    /**
     *
     * @param mixed $email
     * @return \Email
     */
    public function ccAdmin($email = null)
    {
        $email = $this->_formatEmail($email);
        if (is_null($email)) {
            $this->_setCCDefaultAdmin();
        } else {
            if (is_array($email) || is_object($email)) {
                foreach ($email as $e) {
                    $this->mail->addCC($e);
                }
            } else {
                $this->mail->addCC($email);
            }
        }

        return $this;
    }

    private function _setCCDefaultAdmin()
    {
        foreach ($this->_adminEmails() as $email) {
            $this->mail->addCC($email);
        }
    }
    private function _setBCCDefaultAdmin()
    {
        foreach ($this->_adminEmails() as $email) {
            $this->mail->addBCC($email);
        }
    }

    /**
     *
     * @param mixed $email
     * @return \Email
     */
    public function bccAdmin($email = null)
    {
        $email = $this->_formatEmail($email);
        if (is_null($email)) {
            $this->_setBCCDefaultAdmin();
        } else {
            if (is_array($email)) {
                foreach ($email as $e) {
                    $this->mail->addBCC($e);
                }
            } else {
                $this->mail->addBCC($email);
            }
        }
        return $this;
    }

    /**
     *
     * @param mixed $email
     * @return \Email
     */
    public function message($message)
    {
        $this->mail->Body = $message;
        return $this;
    }

    /**
     *
     * @param mixed $email
     * @return \Email
     */
    public function compose($html = true)
    {
        $this->mail->isHTML($html);
        if (!$this->isset_ReplyFrom) {
            $this->noReply();
            $this->from($this->replyFrom);
        }
        return $this->mail->send();
    }

    public static function dev($file, $method, $message, $misc = [])
    {
        $message = '<p>file: ' . $file . ' </p><p>method: ' . $method . '</p><p>misc: ' . json_encode($misc) . '</p><p>message: ' . json_encode($message) . '</p>';
        self::to('jeff@jasoncases.com')->subject('ERROR EMAIL LOG')->message($message)->compose();
    }

    /**
     *
     * @param mixed $email
     * @return \Email
     */
    public function attachments($files = [])
    {
        if (is_null($files)) {
            return $this;
        }

        foreach ($files as $file) {
            extract($file);

            // Arr::pre($file);
            $_dom = '/home/zerodock/public_html';
            $path = $_dom . $root . $filename;

            if (is_file($path)) {
                // echo "<h2>is file true</h2>";
                // echo "[ path: $path ]<br />";
                $this->mail->addAttachment($path);
            } else {
                self::dev('EmployeeHandler.php', 'attachments', $path);
            }
        }

        // Arr::pre($this->mail);
        return $this;
    }

    /**
     * Retrieves all admin email addresses from the Employee table
     * @return array
     */
    private function _adminEmails()
    {
        return array_column(Employee::getAdmin(), 'email');
    }

    /**
     * Returns the same array if an array is supplied. Checks if given
     * attr is a string of multiple email address and explodes it, to
     * return a proper array
     *
     * @param mixed $email
     * @return array|string
     */
    private function _formatEmail($email)
    {
        if (is_null($email)) {
            $email = $this->_defaultTemp;
        }
        if (is_array($email)) {
            return array_filter($email);
        }
        $match = preg_match($this->re, $email);
        return $match
            ? array_filter(explode(',', str_replace(' ', '', $email)))
            : $email;
    }
}