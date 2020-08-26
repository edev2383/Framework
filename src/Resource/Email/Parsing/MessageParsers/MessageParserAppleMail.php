<?php

namespace Edev\Resource\Email\Parsing;

class MessageParserAppleMail extends MessageParser
{

    private $iphoneRegex = '/Sent from/';

    protected function _parseMessage()
    {
        $container = [];
        $capture = false;
        foreach ($this->messageArray as $k => $line) {

            if ($this->_checkAllEndingRegex($line)) {
                break;
            }
            if ($capture) {
                $container[] = $line;
            }
            if ($line === '') {
                $capture = true;
            };
        }
        return $container;
    }

    protected function _extendRegex()
    {
        return [$this->iphoneRegex];
    }

}
