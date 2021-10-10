<?php
/**
 * 引擎加载文件
 * autoload.php
 */

/* Basic Const */
ini_set('date.timezone'  ,'Asia/Shanghai');
define("SERVER_DIR"      , __DIR__.'/');
define('LIB_DIR'         , __DIR__.'/library/');
define('API_DIR'         , dirname(__DIR__).'/api/');
define('TESTER_DIR'      , dirname(__DIR__).'/tester/');
define('WEBSITE_DIR'     , dirname(__DIR__).'/website/');
define('STATIC_DIR'      , dirname(__DIR__).'/website/static/');
define('CUSTOM_DIR'      , __DIR__.'/custom/');

define('CERT_DIR'        , __DIR__.'/cert/');
define('TEMP_DIR'        , __DIR__.'/temp/');
define('KB'              , 1024);
define('MB'              , 1048576 );

if( isset($_GET['_SAASID']) ){ define( 'SaasID', $_GET['_SAASID'] ); }

if( !defined('TIME_START') ) {
    define('TIME_START',microtime(true));
}

if(file_exists(SERVER_DIR.'config.php') ){
    require_once SERVER_DIR.'config.php';
    define('USE_DB_CONFIG',CONFIG['ENABLED_DBMODE']);
}

if( !defined('CONFIG') ){
    define('CONFIG',[]);
}

/* Include Requirements */

require SERVER_DIR."engine/autoload.php";

if( file_exists( SERVER_DIR.'custom/autoload.php' ) ){
    include  SERVER_DIR.'custom/autoload.php';
}

if( file_exists(SERVER_DIR.'custom.php') ){
    include SERVER_DIR.'custom.php';
}

//_ASRedis()->flush();

# Change the default language
if (!isset($_SESSION)) { session_start(); }
if( isset($_GET['setI18n']) ){ $_SESSION['i18n'] = $_GET['setI18n']; }

_I18n()->setLang($_GET['i18n'] ?? $_SESSION['i18n'] ?? 'zh-CN' , false );

//_I18n()->setLang($_GET['i18n'] ?? $_SESSION['i18n'] ?? 'zh-CN' , true );
