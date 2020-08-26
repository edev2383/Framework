<?php

///home/zerodock/public_html
$vendorDir = __DIR__;

// define constants
define('ROOT', $vendorDir . '/src');

$directories =
    [

    'System/Error',
    'Interface',
    'Route',
    'Route/Data',
    'App/Controller',
    'App/Repository',
    'App/Throwable',
    'App/Installer',
    'App/Model',
    'App/Logger',
    'App/Model/Relation',
    'Database/Connector',
    'Database/Container',
    'Database/Manager',
    'Database/Manager/Conn',
    'Database/Model',
    'Database/Query',
    'Database/Query/Processor',
    'Database/Query/Processor/Children',
    'Controller',
    'Middleware',
    'Model',
    'Model/Code',
    'Model/Relation',
    'Model/Attendance',
    'Model/Meta',
    'Model/Voice',
    'Model/Payroll',
    'Model/Client',
    'Model/Employee',
    'Model/Permission',
    'Model/System',
    'Model/Worktype',
    'Model/Timeline',
    'Model/Timesheet',
    'Model/Schedule',
    'Model/Report',
    'Resource/Alert',
    'Resource/PDO/Parent',
    'Resource/PDO/Children',
    'Resource/Display',
    'Resource/Display/Parsers',
    'Resource/Display/Templates',
    'Resource/Timeline',
    'Resource/Payroll',
    'Resource/Timesheet',
    'Resource/Timesheet/Alerts',
    'Resource/Alert/EmailAlert',
    'Resource/Alert/VoiceAlert',
    'Resource/Client',
    'Resource/Product',
    'Resource/Daemon',
    'Resource/Daemon/Alerts',
    'Resource/User',
    'Resource/Email',
    'Resource/Email/Routing',
    'Resource/Email/Piping',
    'Resource/Email/Parsing',
    'Resource/Email/Parsing/MessageParsers',
    'Resource/Email/Template',
    'Resource/Attendance',
    'Resource/Uploads',
    'Resource',
    'Throwable',
    'Repository',
    'Controller/Schedule',
    'Controller/Payroll',
    'Controller/Scoreboard',
    'Controller/Meta',
    'Controller/Attendance',
    'Repository/Announcement',
    'Repository/Code',
    'Repository/Employee',
    'Repository/Payroll',
    'Repository/Schedule',
    'Repository/System',
    'Repository/Timesheet',
    'Repository/Worktype',
    'System/Helpers',
    'System/Logger',
    'System/Logger/Levels',

];

// loop through directories and include all php files therein
foreach ($directories as $value) {
    foreach (glob(ROOT . "/$value/*.php") as $filename) {
        // echo "<p>$filename</p>";
        include $filename;
    }
}
