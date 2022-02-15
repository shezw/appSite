<?php
/**
 * 网站端首页
 * HomePage of website
 *
 * @link https://appsite.cn
 * @author Sprite Shur  https://shezw.com  hello@shezw.com
 * @copyright shezw.com
 * @version 2.0
 */

use APS\Website;

define('TIME_START',microtime(true));

include_once dirname(__DIR__).'/server/autoload.php';

define('SITE_DIR' , __DIR__.'/');
define('SITE_ROUTE_DEFAULT',SITE_DIR.'route/default/');
define('SITE_ROUTE_CUSTOM',SITE_DIR.'route/custom/');
define('THEME_DIR', SITE_DIR .'themes/'. (getConfig("theme",RouteScopeWebsite) ?? WebsiteDefaultTheme).'/' );


/** 设定网站本地化语言
 *  Set i18n Code for website
 */
//_I18n()->setLang($_GET['i18n'] ?? $_SESSION['i18n'] ?? 'zh-CN' ,true );
_I18n()->setLang($_GET['i18n'] ?? $_SESSION['i18n'] ?? 'zh-CN'  );

$website = new Website(getConfig('WEBSITE_ROUTE_FORMAT') ?? WebsiteDefaultRouteFormat );

/**
 * 引入路由入口文件
 */

if(file_exists(SITE_ROUTE_CUSTOM.'entry.php')){
    include SITE_ROUTE_CUSTOM.'entry.php';
}else if(file_exists( SITE_ROUTE_DEFAULT.'entry.php' )){
    include SITE_ROUTE_DEFAULT.'entry.php';
}else{
    $website->take('Route Entry not found.')->error(1000,'Fail');
    $website->export();
}