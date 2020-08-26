<?php

namespace Edev\Resource;

class FlashAlert
{
    public function __construct($message, $status = 'success')
    {
        $this->array['status'] = strtolower($status);
        $this->array['msg'] = $message;

        if (strtolower($status) != 'success') {
            // ERROR LOGGING GOES HERE
        }
        $_SESSION['response'] = $this->encode();
    }

    private function encode()
    {
        return json_encode($this->array, JSON_PRETTY_PRINT);
    }
}
