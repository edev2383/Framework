<?php

namespace Edev\Resource\Email\Parsing;

class MessageParser
{

    protected $messageArray;
    protected $onWroteDayRegex = '/On (Mon|Tue|Wed|Thu|Fri|Sat|Sun)/';
    protected $onWroteMonthRegex = '/On (Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)/';

    public function __construct($messageArray)
    {
        $this->messageArray = $this->_removeLeadingEmtpyRows($messageArray);
    }

    public function run()
    {
        $stagingMessage = $this->_finalFormatting(
            $this->_parseMessage()
        );
        // echo "<h3>run()</h3>";
        // Arr::pre($stagingMessage);
        return $stagingMessage;
    }

    private function _finalFormatting($messageArray)
    {
        $messageArray = $this->_removeLeadingEmtpyRows($messageArray);
        $messageArray = $this->_removeTrailingEmptyRows($messageArray);
        return $this->_messageFormatter($messageArray);
    }

    private function _messageFormatter($messageArray)
    {
        return MessageFormatter::format($messageArray);
    }

    private function _removeTrailingEmptyRows($message)
    {
        while (end($message) === '') {
            array_pop($message);
        }
        return $message;
    }

    protected function _parseMessage()
    {}

    private function _removeLeadingEmtpyRows($messageArray)
    {
        while (current($messageArray) === '') {
            array_shift($messageArray);
        }
        return $messageArray;
    }

    protected function _checkAllEndingRegex($line)
    {
        $regex = [$this->onWroteDayRegex, $this->onWroteMonthRegex];
        $regex = array_merge($regex, $this->_extendRegex());
        foreach ($regex as $re) {
            if (preg_match($re, $line, $M)) {
                return true;
            }
        }
        return false;
    }

    protected function _extendRegex()
    {
        return [];
    }
}
