<?php

namespace Edev\Resource\Email\Parsing;

class MessageParserGmail extends MessageParser
{

    private $re_gmail = '/^--[0-9a-zA-Z]+/';

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

            $x_line = str_replace(' ', '', $line);

            if ($x_line === '') {
                $capture = true;
            };
        }

        return $container;
    }

    protected function _extendRegex()
    {
        return [];
    }
}
