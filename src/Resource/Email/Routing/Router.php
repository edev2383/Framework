<?php

namespace Edev\Resource\Email\Routing;

use Edev\Resource\Email\Routing\TicketReply;

class Router
{

    /**
     *
     *
     * */
    public function __construct($email)
    {
        // mail('jeff@jasoncases.com', 'input', print_r($email, true), '', ''); // ! DEBUG
        $match = '/^To: (.*)/';
        $to = current(
            array_filter(
                $email,
                function ($v, $k) use ($match) {
                    return preg_match($match, $v, $matches);
                },
                ARRAY_FILTER_USE_BOTH
            )
        );

        if (preg_match('/.*ticket.*/', $to) == 1) {
            $emailParser = new \Edev\Resource\Email\Parser($email);
            new TicketReply($emailParser);
        } else {
            $pipe = new \Edev\Resource\Email\Piping($email);
            $pipe->format()->send();
        }
    }
}
