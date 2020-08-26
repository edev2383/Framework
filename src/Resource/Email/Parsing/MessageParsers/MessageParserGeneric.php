<?php

namespace Edev\Resource\Email\Parsing;

class MessageParserGeneric extends MessageParser
{
    protected function _parseMessage()
    {
        mail('jeff@jasoncases.com', 'MessageParserGeneric', print_r($this->messageArray, true));
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
