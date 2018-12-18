<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

//OTHER CONSTANTS
define('VERITRANS_SERVER_KEY',					'VT-server-EwRjGJImjYaQN-ls707v5GJz'); //VT-server-_0gvzoqeTaOgckh25tBd_y9Z
define('VERITRANS_CLIENT_KEY',					'VT-client-duA-8byM6-f_tQ2c');
define('VERITRANS_MERCHANT_ID',					'M063888');
define('VERITRANS_IS_PRODUCTION', 				false);

define('BOX',									'green-haze');

define('SESSION_NAME',                          'sess_dwijaya_project');

define('PROJECT',								'Citi Home Collection');
define('PROJECT_SHORT', 						'CHC');
define('SERVER_IS_PRODUCTION', 				    false);
define('WKHTML_LINUX',         				    '/usr/local/bin/wkhtmltopdf'); //usr/local/bin/wkhtmltopdf
define('WKHTML_WINDOWS',         				'C:/wkhtml/wkhtmltopdf/bin/wkhtmltopdf');
define('ALLOW_WIFI_GEN',                        false);

define('STATUS_INACTIVE',						0);
define('STATUS_NEW',							1);
define('STATUS_EDIT',							2);
define('STATUS_PROCESS',						3);
define('STATUS_APPROVE',						4);
define('STATUS_DISAPPROVE',						5);
define('STATUS_CANCEL',							6);
define('STATUS_POSTED',							7);
define('STATUS_CLOSED',							8);
define('STATUS_DELETE',							9);
define('STATUS_AUDIT',							10);
define('STATUS_VIEW',							11);
define('STATUS_REJECT',							12);
define('STATUS_CASH',							13);
define('STATUS_UNLOCK',							14);
define('STATUS_PRINT',							15);


define('DOC_SO',							16);
define('DOC_DO',							17);
define('DOC_INV',							17);
/* End of file constants.php */
/* Location: ./application/config/constants.php */



