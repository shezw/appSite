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
define('THEME_DIR', SITE_DIR .'themes/'. (getConfig("theme",RouteScopeWebsite) ?? WebsiteDefaultTheme).'/' );


/** 设定网站本地化语言
 *  Set i18n Code for website
 */
//_I18n()->setLang($_GET['i18n'] ?? $_SESSION['i18n'] ?? 'zh-CN' ,true );
_I18n()->setLang($_GET['i18n'] ?? $_SESSION['i18n'] ?? 'zh-CN'  );

$website = new Website(getConfig('WEBSITE_ROUTE_FORMAT') ?? WebsiteDefaultRouteFormat );

/**
 * 引入主题文件
 */
if ( file_exists(THEME_DIR.'index.php') ){
    include_once THEME_DIR.'index.php';
}else{
    $website->take('Theme "'.(getConfig('theme',RouteScopeWebsite)??'default').'" not found.')->error(1000,'Fail');
    $website->export();
}
