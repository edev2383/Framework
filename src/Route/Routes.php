<?php

namespace Edev\Route;

// Resource Routes
$route = Route::getInstance();

// MOCK CONTROLLER
$route::resource('fake', 'FakeController');
$route::controller('fake/{action:string}', 'FakeController', 'getFakeTimesheetAction');
// Employee Resource Group
$route::resource('emp', 'EmpController');
$route::resource('dept', 'DepartmentController');
$route::resource('user', 'UserController');

// Timesheet Resource Group
$route::resource('timesheet', 'TimesheetController');
$route::resource('timeline', 'TimelineController');
$route::resource('worktype', 'WorkController');
$route::resource('timeclock', 'TimeclockController');

// Payroll Resource Group
$route::resource('payroll', 'PayrollController');
$route::resource('pto', 'PayrollPtoController', 'Payroll');
$route::resource('report', 'ReportController');
$route::resource('simple', 'SimpleController');

// Announcement Resource Group
$route::resource('voice', 'VoiceController');

// Code Resource Group
$route::resource('code', 'CodeController');

// Misc Resource Group
$route::resource('huddle', 'HuddleController');
$route::resource('prod', 'ProductionController');

// Test
$route::resource('scan', 'ScanController');

// Schedule Resource Group
$route::resource('schedule', 'ScheduleController');
$route::resource('shift', 'ScheduleShiftController', 'Schedule');
$route::resource('availability', 'ScheduleAvailabilityController', 'Schedule');
$route::resource('request', 'ScheduleRequestController', 'Schedule');
$route::resource('swap', 'ScheduleSwapController', 'Schedule');
$route::resource('paat', 'AttendanceController', 'Paat');
// $route::resource('stamp', 'ScheduleStampController', 'Schedule');

// Scoreboard Resource Group
// $route::resource('scoreboard', 'ScoreboardController', 'Scoreboard');
// $route::resource('environment', 'EnvironmentController', 'Scoreboard');

// System Setting Resource Group
$route::resource('settings', 'SettingsController');
$route::resource('permissions', 'PermissionController');
$route::resource('api', 'ApiController');
//$route::resource('custom', 'CustomController');

// TODO - TESTING CLIENT CREATION
$route::resource('account', 'MetaController', 'Meta');
$route::resource('verify', 'VerifyController');
$route::resource('subdomain', 'SubdomainController');
$route::resource('muser', 'MetaUserController', 'Meta');
$route::resource('pwr', 'PasswordResetController');

/** ****************************************************************************************
 * Begin Custom Route Controllers
 ***************************************************************************************** */

// Password reset
$route::controller('pwr/{token:string}/{email:string}', 'PasswordResetController', 'getVerifyToken');
$route::controller('pwr/{email:string}/4/refresh', 'PasswordResetController', 'getRefreshToken');
// VERIFY ROUTES
$route::controller('verify/{id}/renew', 'VerifyController', 'getRenewVerificationToken');

// SUBDOMAIN & SYSTEM ROUTES
$route::controller('subdomain/resethttpd', 'SubdomainController', 'getResetHttpd');
$route::controller('subdomain/resetrndc', 'SubdomainController', 'getResetRndc');

// META ACCESS ROUTE
$route::controller('account/login', 'MetaController', 'postAccountLogin', 'POST');
$route::controller('account/login', 'MetaController', 'getAccountLogin');
$route::controller('account/user/create', 'MetaController', 'getAccountCreateAdminUser');
$route::controller('account/user', 'MetaController', 'postAccountCreateAdminUser', 'POST');
$route::controller('account/finalize', 'MetaController', 'putFinalizeAccount', 'PUT');

// $route::controller('account/finalize', 'MetaController', 'putFinalizeAccount', 'PUT');

// Payroll Handling Routes
$route::controller('payroll/test', 'PayrollController', 'getTest');
$route::controller('payroll/clockedin', 'PayrollController', 'getClockedIn');
$route::controller('payroll/onbreak', 'PayrollController', 'getOnBreak');
$route::controller('payroll/status', 'PayrollController', 'getStatus');
$route::controller('payroll/statustoo', 'PayrollController', 'getStatusToo');
$route::controller('payroll/addcomment', 'PayrollController', 'postAddComment', 'POST');
$route::controller('payroll/createflag', 'PayrollController', 'postCreateManualFlag', 'POST');
$route::controller('payroll/updateflag', 'PayrollController', 'putUpdateManualFlag', 'PUT');
$route::controller('payroll/location', 'PayrollController', 'postLocationIp', 'POST');
$route::controller('payroll/total', 'PayrollController', 'getTest');
$route::controller('payroll/shifts/{id}/{from:string}/{to:string}', 'PayrollController', 'getShifts');
$route::controller('payroll/shifts/{id}', 'PayrollController', 'getShiftsRedirect');
$route::controller('payroll/stamp/{id}', 'PayrollController', 'getStamp');
$route::controller('payroll/record/reviewed', 'PayrollController', 'putReviewed', 'PUT');

// PAYROLL SUB-NAMESPACE CONTROLLERS
$route::controller('pto/test', 'PayrollPtoController', 'getTest');
$route::controller('pto/all', 'PayrollPtoController', 'getAllPtoAdmin');
$route::controller('pto/showall', 'PayrollPtoController', 'getShowAll');

// Timesheet Handler Routes
$route::controller('timesheet/snapshot', 'TimesheetController', 'getSnapshot'); // user_page snapshot
$route::controller('timesheet/bydate', 'TimesheetController', 'getDataByDate'); // bydate method for snapshot timing
$route::controller('timesheet/test', 'TimesheetController', 'getTest'); // method for random testing
// ! - MARKED FOR DELETION - $route::controller('timesheet/{id}/break', 'TimesheetController', 'getBreakStatus');
// ! - MARKED FOR DELETION - $route::controller('timesheet/allowed', 'TimesheetController', 'getAllowedStates');
// ! - MARKED FOR DELETION - $route::controller('timesheet/allstates', 'TimesheetController', 'getAllButtonStates');
// ! - MARKED FOR DELETION - $route::controller('timesheet/reviewed/{id}', 'TimesheetController', 'putReviewedStatus', 'PUT');

// Timeline Route
$route::controller('timeline/{id}/cascade/{date:string}', 'TimelineController', 'getCascade');

// Code Tracker Handling Routes
$route::controller('code/review', 'CodeController', 'getCodeReview');
$route::controller('code/acknowledge', 'CodeController', 'postCodeAcknowledge', 'POST');
$route::controller('code/read', 'CodeController', 'getCodes');
$route::controller('code/total', 'CodeController', 'getTotalNumberOfCodes');
$route::controller('code/entry', 'CodeController', 'getEntryIndex');
$route::controller('code/search', 'CodeController', 'getCodeSearch');
$route::controller('code/search', 'CodeController', 'postCodeSearch', 'POST');
$route::controller('code/reminder', 'CodeController', 'postCodeReminder', 'POST');
$route::controller('code/reminder', 'CodeController', 'getCodeReminder');
$route::controller('code/comment', 'CodeController', 'getCodeComment');
$route::controller('code/addcomment', 'CodeController', 'postEnterCodeComment', 'POST');
$route::controller('code/mgrreview', 'CodeController', 'postMarkAsReviewed', 'POST');
$route::controller('code/codeempstate', 'CodeController', 'getEmployeeStateObject');
$route::controller('code/score', 'CodeController', 'getCodeScores');
$route::controller('code/test', 'CodeController', 'getTest');
$route::controller('code/mobile', 'CodeController', 'getMobileTracker');
$route::controller('code/comms', 'CodeController', 'postCodeStaffComms', 'POST');

// ! marked for deletion, active for testing
// ! delete by 6/7/20 if no emails received from route access
$route::controller('code/addcomment', 'CodeController', 'getEnterCodeComment');

// Login/Logout Routes
// $route::controller('user/login', 'UserController', 'postLogin', 'POST');
$route::controller('user/status', 'UserController', 'getUserStatus');
$route::controller('login', 'HomeController', 'getLogin');
$route::controller('logout', 'HomeController', 'getLogout');
$route::controller('pin', 'UserController', 'postLoginWithPin', 'POST');
$route::controller('process', 'UserController', 'postLoginWithEmail', 'POST');
$route::controller('user/setcookie', 'UserController', 'getSetCookie');
// ------------------- // Login/Logout Subset Routes
$route::controller('landing', 'HomeController', 'getUserPage');
$route::controller('badge', 'HomeController', 'getBadge');
$route::controller('devlinks', 'HomeController', 'getAllRoutes');
$route::controller('sess', 'HomeController', 'getSession');
$route::controller('sess', 'HomeController', 'postSession', 'POST');
$route::controller('client', 'HomeController', 'getClient');
$route::controller('user/hours', 'UserController', 'getUserHours');

// Edit User Settings Routes
$route::controller('user/{id}/edit/pin', 'UserController', 'getEditPin');
$route::controller('user/{id}/edit/pin', 'UserController', 'putSetPin', 'PUT');
$route::controller('user/permission/remove/{id}', 'UserController', 'putRemovePermission', 'PUT');
$route::controller('user/permission/add', 'UserController', 'postAddPermission', 'POST');
$route::controller('user/status/change', 'UserController', 'getUserStatusChange');

// Employee Handling Routes
$route::controller('emp/toggle/{id}', 'EmpController', 'putToggle', 'PUT');
$route::controller('emp/newcreate', 'EmpController', 'getNewCreate');

// Flash Status Response Handling Routes
$route::controller('response', 'HomeController', 'getResponse');
$route::controller('response/clear', 'HomeController', 'getClearResponse');

// Global Handler Route
$route::controller('envvar', 'HomeController', 'getGlobalVar');

// Voice handler routes
$route::controller('voice/timed', 'VoiceController', 'getTimedMessages');
$route::controller('voice/play', 'VoiceController', 'getVoiceAnnouncements');
$route::controller('voice/chrome', 'VoiceController', 'getVoiceAnnouncementsChromeExtension');
$route::controller('voice/history', 'VoiceController', 'getVoiceAnnouncementHistory');
// $route::controller('voice/scheduled', 'VoiceController', 'getScheduledAnnouncement');
$route::controller('voice/scheduled', 'VoiceController', 'postScheduledAnnouncement', 'POST');
$route::controller('voice/scheduled/{id}', 'VoiceController', 'deleteScheduledAnnouncement', 'DELETE');
$route::controller('voice/upcoming', 'VoiceController', 'getUpcomingAnnouncements');
$route::controller('voice/tally', 'VoiceController', 'getDailyTally');
$route::controller('voice/scheduled', 'VoiceController', 'getScheduledAnnouncements');
$route::controller('voice/portal/show/{id}', 'VoiceController', 'getClientVoiceAnnouncements');
$route::controller('voice/portal/quick', 'VoiceController', 'getClientQuickAnnouncements');
$route::controller('voice/portal/categories', 'VoiceController', 'getClientVoiceAnnouncementCategories');
$route::controller('voice/portal/voices', 'VoiceController', 'getVoiceAnnouncementVoices');
$route::controller('voice/portal/client', 'VoiceController', 'postClientVoiceAnnouncement', 'POST');
$route::controller('voice/portal/client', 'VoiceController', 'putUpdateClientVoiceAnnouncement', 'PUT');
$route::controller('voice/portal/recurring', 'VoiceController', 'postRecurringVoiceAnnouncement', 'POST');
$route::controller('voice/portal/recurring', 'VoiceController', 'putUpdateRecurringVoiceAnnouncement', 'PUT');
$route::controller('voice/portal/recurring/{id}', 'VoiceController', 'getRecurringAnnouncementById');
$route::controller('voice/portal/client/{id}', 'VoiceController', 'getCategoryAnnouncementById');
$route::controller('voice/portal/recurring/{id}', 'VoiceController', 'deleteRecurringAnnouncement', 'DELETE');
$route::controller('voice/portal/client/{id}', 'VoiceController', 'deleteClientAnnouncement', 'DELETE');

// Work type Handlers
$route::controller('worktype/category/create', 'WorkController', 'getWorktypeCategoryCreate');
$route::controller('worktype/category/create', 'WorkController', 'postWorktypeCategoryStore', 'POST');
$route::controller('worktype/read', 'WorkController', 'getAllCustomWorkTypes');
$route::controller('worktype/report', 'WorkController', 'getCustomWorktypeReport');
$route::controller('worktype/adddept', 'WorkController', 'postDepartmentToWorktype', 'POST');
$route::controller('worktype/removedept', 'WorkController', 'postRemoveDepartmentFromWorktype', 'POST');
$route::controller('worktype/showworktype/{id}', 'WorkController', 'getShowWorktype');

// Permission handlers
// $route::controller('permissions/category', 'PermissionController', 'postPermissionAssignCategory', 'POST'); ! Removed
$route::controller('permissions/storedetails', 'PermissionController', 'postStorePermissionAccessDetails', 'POST');

$route::controller('stream', 'StreamController', 'getStream');

// Schedule routes
$route::controller('schedule/snapshot', 'ScheduleController', 'getScheduleSnapshot');
$route::controller('schedule/current', 'ScheduleController', 'getCurrentSchedule');
$route::controller('schedule/employees', 'ScheduleController', 'getEmployeesForSchedule');
$route::controller('schedule/commit/{id}', 'ScheduleController', 'putCommitSchedule', 'PUT');
$route::controller('schedule/statusbystartdate', 'ScheduleController', 'getScheduleStatus');
$route::controller('schedule/load', 'ScheduleController', 'getScheduleIdByDate');
$route::controller('schedule/textoutput/{id}', 'ScheduleController', 'getOutputFullSchedule');
$route::controller('schedule/tmp/{id}', 'ScheduleController', 'getTempScheduleOutput');
$route::controller('schedule/test', 'ScheduleController', 'getTest');
$route::controller('schedule/state/{id}', 'ScheduleController', 'putScheduleState', 'PUT');
$route::controller('schedule/dashboard', 'ScheduleController', 'getDashboard');
$route::controller('schedule/dashboard/test', 'ScheduleController', 'getTestNewRender');
$route::controller('schedule/post', 'ScheduleController', 'postSchedule', 'POST');
$route::controller('schedule/{date:string}', 'ScheduleController', 'getScheduleByDate');

$route::controller('shift/batch', 'ShiftController', 'postBatchShifts', 'POST');
$route::controller('shift/batch/{id}', 'ShiftController', 'deleteAllShiftsByScheduleId', 'DELETE');

$route::controller('shift/{id}/stamp/status', 'ScheduleShiftController', 'getScheduleStampStatus');

// $route::controller('stamp/{id}/status', 'ScheduleStampController', 'getStatus');

// Settings routes
$route::controller('settings/{id}/user', 'SettingsController', 'getUserSettings');
$route::controller('settings/client', 'SettingsController', 'getClientSettings');
$route::controller('settings/client/{constant:string}', 'SettingsController', 'getClientGlobal');
$route::controller('settings/client/load/{category:string}', 'SettingsController', 'getClientGlobalCategory');

// Log routes
$route::controller('log', 'LogController', 'postSend', 'POST');

// Prod routes
$route::controller('prod/prod', 'ProductionController', 'getProd');
$route::controller('prod/woo', 'ProductionController', 'getWooCommerceNumbers');
$route::controller('prod/csvoutput', 'ProductionController', 'getCsvOutput');
$route::controller('prod/weeklypayroll', 'ProductionController', 'getWeeklyPayroll');
$route::controller('prod/departments', 'ProductionController', 'getDepartments');
$route::controller('prod/metrics', 'ProductionController', 'getMetrics');
$route::controller('prod/outputcsv', 'ProductionController', 'postCsvOutputAlt', 'POST');

// Search
$route::controller('search', 'SearchController', 'getFuzzySearch');

// Custom
// $route::controller('custom/gluetimer', 'CustomController', 'getGlueTime');
// $route::controller('custom/gluetimer', 'CustomController', 'postGlueTime', 'POST');
// $route::controller('custom/gluetimer/report', 'CustomController', 'getGlueTimerReport');
// $route::controller('custom/gluetimer/test', 'CustomController', 'getTest');

// Department Routes
$route::controller('dept/addemp', 'DepartmentController', 'postAssignEmployeeDepartment', 'POST');
$route::controller('dept/removeemp/{id}', 'DepartmentController', 'putUnassignEmployeeDepartment', 'PUT');
$route::controller('dept/unassigned', 'DepartmentController', 'getUnassignedEmployees');
$route::controller('dept/newrole', 'DepartmentController', 'postRole', 'POST');
$route::controller('dept/removerole/{id}', 'DepartmentController', 'deleteRole', 'DELETE');

// API
$route::controller('api/schedule', 'ApiController', 'getCurrentScheduleData');
$route::controller('api/client/uids', 'ApiController', 'getClientUids');
$route::controller('api/voice', 'ApiController', 'postPlayVoiceAnnouncement', 'POST');
$route::controller('api/voice', 'ApiController', 'getPlayVoiceAnnouncement');
$route::controller('api/voice/{token:string}/{message:string}', 'ApiController', 'postPlayVoiceAnnouncementWC', 'POST');
// $route::controller('api/voice/{token::string}/{message::string}', 'ApiController', 'getTestAnnouncement');
$route::controller('api/daemon/alert', 'ApiController', 'postTimeclockAlert', 'POST');
$route::controller('api/daemon/alert/{type:string}/{rule:string}/{employeeId:int}', 'ApiController', 'getTimeclockAlert');

// $route::controller('api/voice/{token::string}/{message::string}', 'ApiController', 'getTest');

$route::controller('request/view', 'ScheduleRequestController', 'getRequestView');
$route::controller('request/add/{id}', 'ScheduleRequestController', 'postNewRequest', 'POST');
$route::controller('request/all', 'ScheduleRequestController', 'getAllRequestsAdmin');
$route::controller('request/{id}/show', 'ScheduleRequestController', 'getShow');
$route::controller('request/{id}/{date:string}', 'ScheduleRequestController', 'getShowByWeek');

// Scoreboard
// $route::controller('scoreboard/{name::string}', 'ScoreboardController', 'getModule');
//$route::controller('scoreboard/shippedtoday', 'ScoreboardController', 'getShippedToday');

// REPORT
$route::controller('report/test', 'ReportController', 'getTest');
$route::controller('report/hoursoutput', 'ReportController', 'getReportHoursAndOutput');
$route::controller('report/clockedin', 'ReportController', 'getReportClockedIn');
$route::controller('report/weeklyhours', 'ReportController', 'getReportWeeklyHours');
