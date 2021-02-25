<?php
/**
 * 引擎加载文件
 * autoload.php
 */

/* Basic Const */
ini_set('date.timezone'  ,'Asia/Shanghai');
define("SERVER_DIR"      , __DIR__.'/');
define('API_DIR'         , dirname(__DIR__).'/api/');
define('WEBSITE_DIR'     , dirname(__DIR__).'/website/');
define('STATIC_DIR'      , dirname(__DIR__).'/website/static/');
define('CERT_DIR'        , __DIR__.'/cert/');
define('KB'              , 1024);
define('MB'              , 1048576 );

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
session_start();
if( isset($_GET['setI18n']) ){ $_SESSION['i18n'] = $_GET['setI18n']; }

_I18n()->setLang($_GET['i18n'] ?? $_SESSION['i18n'] ?? 'zh-CN' , false );
//_I18n()->setLang($_GET['i18n'] ?? $_SESSION['i18n'] ?? 'zh-CN' , true );
