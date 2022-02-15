<?php

/**
 * 管理后台首页
 * Homepage of Backend management
 *
 * 管理后台实际上是一个扩展的动态网站前台,基于Website模块进行分组、导航的精细控制即可实现
 * The Back-End management is actually an extended dynamic website
 *
 * @link https://appsite.cn
 * @author Sprite Shur  https://shezw.com  hello@shezw.com
 * @copyright shezw.com
 * @version 2.0
 */

use APS\Management;

include_once dirname(__DIR__).'/server/autoload.php';

define('SITE_DIR' , __DIR__.'/');
define('SITE_ROUTE_DEFAULT',SITE_DIR.'route/default/');
define('SITE_ROUTE_CUSTOM',SITE_DIR.'route/custom/');
define('THEME_DIR', SITE_DIR .'themes/'. (getConfig("theme",RouteScopeManagement) ?? ManagementDefaultTheme).'/' );

$website = new Management(getConfig('MANAGER_ROUTE_FORMAT') ?? ManagementDefaultRouteFormat );

/** 设定网站本地化语言
 *  Set i18n Code for website
 */
//_I18n()->setLang($_GET['i18n'] ?? $_SESSION['i18n'] ?? 'zh-CN' ,true );
//_I18n()->setLang($_GET['i18n'] ?? $_SESSION['i18n'] ?? 'zh-CN'  );

//var_dump(\APS\CommerceProduct::class);
/**
 * 引入对应文件

路由格式 Route Format
class/action/id

常量
THEME_DIR
PAGE_DIR
CLASS_DIR
COMMON_DIR

基础网站变量
$website
- route     路由
- user      用户对象
- querys    请求字符串
- userData  用户基础数据
 *
 */

if( isset($website->route['action']) ) {  # Class Action
    $filePath = 'class/'.$website->route['class'].'/'.$website->route['action'].'.php';
}else if( isset($website->route['class']) ){  # Page
    $filePath = 'page/'.$website->route['class'].'.php';
}else{ # Home
    $filePath = 'page/dashboard.php';
}

if(file_exists(SITE_ROUTE_CUSTOM.$filePath)){
    include SITE_ROUTE_CUSTOM.$filePath;
}else if(file_exists( SITE_ROUTE_DEFAULT.$filePath )){
    include SITE_ROUTE_DEFAULT.$filePath;
}else{
    file_exists(SITE_ROUTE_DEFAULT.'page/404.php') && $website->to404();
    APS\Mixer::debug("<h1>404 Page Not found.</h1><p>This page not reachable or not exists.</p><a href='/'>Back Home</a>");
}

