<?php

// register_shutdown_function('shutdown_error');

function shutdown_error()
{
    $errfile = "unknown file";
    $errstr = "shutdown";
    $errno = E_CORE_ERROR;
    $errline = 0;

    $error = error_get_last();

    if ($error !== null) {
        $errno = $error["type"];
        $errfile = $error["file"];
        $errline = $error["line"];
        $errstr = $error["message"];

        error_mail(format_error($errno, $errstr, $errfile, $errline));
    }
}

function format_error($errno, $errstr, $errfile, $errline)
{
    $trace = print_r(debug_backtrace(false), true);

    $content = "
    <table>
        <thead><th>Item</th><th>Description</th></thead>
        <tbody>
            <tr>
                <th>Error</th>
                <td><pre>$errstr</pre></td>
            </tr>
            <tr>
                <th>Errno</th>
                <td><pre>$errno</pre></td>
            </tr>
            <tr>
                <th>File</th>
                <td>$errfile</td>
            </tr>
            <tr>
                <th>Line</th>
                <td>$errline</td>
            </tr>
            <tr>
                <th>Trace</th>
                <td><pre>$trace</pre></td>
            </tr>
        </tbody>
    </table>";
    return $content;
}

function error_mail($err)
{

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    $to = ['jeff@jasoncases.com'];
    $subject = '******* Edev FATAL ERROR *********';
    return mail(implode(', ', $to), $subject, $err, $headers);
}