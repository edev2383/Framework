<?php

namespace Edev\Resource\Email;

use Edev\Resource\Email\Parsing\MessageParserFactory;

class Parser
{

    public $to;
    public $from;
    public $subject;
    public $message;

    private $rawEmailArray;
    private $workingMessageArray;
    private $messageArray;
    private $_logFlag = true;

    private $reBreak = '/REPLY ABOVE THIS LINE/';

    public function __construct($email)
    {
        mail('jeff@jasoncases.com', 'Email parser capture', print_r($email, true));
        $this->rawEmailArray = $email;
        $this->to = $this->_addressee($email);
        $this->from = $this->_from($email);
        $this->subject = $this->_subject($email);
        $this->_cleanUpMessage();
    }

    private function _cleanUpMessage()
    {
        $this->_trimHeaders();
        $this->_cleanMessage();
        $this->_logAll();
    }

    private function _logAll()
    {
        if ($this->_logFlag) {
            // Log::info('Email through Edev\Resource\Email\Parser', [
            //     'to' => $this->to,
            //     'from' => $this->from,
            //     'subject' => $this->subject,
            //     'message' => $this->message,
            // ]);
        }
    }
    private function _revertMessageToString()
    {
        $this->message = implode('<br />', $this->messageArray);
    }

    private function _trimHeaders()
    {
        // set init state and message values
        $is_header = true;
        $is_footer = false;
        $message = [];
        $len = count($this->rawEmailArray);
        $lines = $this->rawEmailArray;

        for ($i = 0; $i < $len; $i++) {

            if (preg_match($this->reBreak, $lines[$i])) {
                break;
            }

            // If we're not in the header, we're in the message until we break out
            if (!$is_header) {
                $message[] = $lines[$i];
            }

            // hit an empty line and now we're in the message content
            if (trim($lines[$i]) == "" && $is_header == true) {
                $is_header = false;
            }
        }
        $this->workingMessageArray = $message;
    }

    // private function _createNewMe

    private function _cleanMessage()
    {
        $this->message = MessageParserFactory::create($this->workingMessageArray);
    }

    /**
     * Process method to create the _addressee value
     *
     * @return void
     */
    private function _addressee($email)
    {
        $re = '/^To: (.*)/';
        $to = current(array_filter($email, function ($v, $k) use ($re) {
            return preg_match($re, $v, $matches);
        }, ARRAY_FILTER_USE_BOTH));
        return str_replace('To: ', '', $this->_cleanAddressee($to));
    }

    private function _cleanAddressee($rawTo)
    {
        $emailWithNameRe = '/<(.*?)>/';
        if (preg_match($emailWithNameRe, $rawTo, $m)) {
            return $m[1];
        }
        return $rawTo;
    }

    /**
     * Process method to create the _from value
     *
     * @return void
     */
    private function _from($email)
    {
        $re = '/^"?From (.*)/';
        $from = current(array_filter($email, function ($v, $k) use ($re) {
            return preg_match($re, $v, $matches);
        }, ARRAY_FILTER_USE_BOTH));

        $from = str_replace('"From ', '', $from);
        $from = str_replace('From ', '', $from);
        $fromParts = explode(' ', $from);
        $fromEmail = array_shift($fromParts);
        $timestamp = implode(' ', $fromParts);
        return $fromEmail;
    }

    /**
     * Process method to create the _subject value
     *
     * @return void
     */
    private function _subject($email)
    {
        $re = '/^Subject: (.*)/';
        $subject = current(array_filter($email, function ($v, $k) use ($re) {
            return preg_match($re, $v, $matches);
        }, ARRAY_FILTER_USE_BOTH));

        $subject = str_replace('Subject: ', '', $subject);

        return $subject;
    }
}