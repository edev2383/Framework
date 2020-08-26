<?php

namespace Edev\Resource\Alert;

use Edev\Model\System\AutomatedAlert;
use Edev\System\Helpers\Arr;

/**
 * Voice alert takes the incoming template and creates a new voice
 * announcement, via \Edev\Model\VoiceAnnouncement
 */
class VoiceAlert extends Alert
{

    protected $type = 'voice';

    /**
     * Store a new voice announcement to the database, ultimately
     * returning a boolean status value
     *
     * @return bool
     */
    protected function _send($expiration = 0): bool
    {
        $message = \Edev\Resource\Templater::parse($this->template, $this->data);
        if ($this->_archiveMessage((string) $message, $expiration)) {
            return \Edev\Model\VoiceAnnouncement::sendNewAnnouncement(
                $message,
                '2',
                'node',
            );
        }
        return false;
    }

    /**
     * Returns false if a `fresh` message exists in the database w/ a 
     * matching hash
     *
     * @param [type] $message
     * @return void
     */
    private function _archiveMessage($message, $expiration = 0)
    {
        return AutomatedAlert::scan($message, $this->employeeId, $expiration);
    }
}