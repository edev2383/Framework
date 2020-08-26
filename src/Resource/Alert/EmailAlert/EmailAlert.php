<?php

namespace Edev\Resource\Alert;

use Edev\Resource\Email;
use Edev\System\Helpers\Str;

/**
 * Child class of Alert(), sends email alerts
 */
class EmailAlert extends Alert
{
    protected $type = 'email';


    /**
     * Break the Alert->template into it's component subject/message
     * parts and pass to the _sendEmail method
     *
     * @return bool
     */
    protected function _send($expiration = 0): bool
    {
        extract(json_decode($this->template, true));
        $subject = \Edev\Resource\Templater::parse($subject, $this->data);
        $message = \Edev\Resource\Templater::parse($message, $this->_formatData());
        return $this->_sendEmail($subject, $message);
    }

    private function _formatData()
    {
        $max_hours = number_format(current(\Edev\Model\Meta\ClientGlobal::get('unix_auto_clockout')) / 3600, 2, '.', '');
        return array_merge(compact('max_hours'), $this->data);
    }

    /**
     * Sned the email
     *
     * @param string $subject
     * @param [type] $message
     * @return bool 
     */
    private function _sendEmail(string $subject, $message): bool
    {
        // TODO - return the auxHeadersCopyAdmin version.
        // $email->to($this->data['email'])->subject($subject)->message($message)->headersHTMLNoReply()->auxHeadersCopyAdmin()->compose();
        return Email::to($this->data['email'])
            ->subject($subject)
            ->message($message)
            ->compose();
    }
}
