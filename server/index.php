<?php 
/**
 * AppSite Engine Server Side
 * 服务端入口
 *
 * @link https://appsite.cn
 * @author Sprite Shur  https://shezw.com  hello@shezw.com
 * @copyright shezw.com
 * @version 2.0
 */

require_once __DIR__.'/autoload.php';

_ASRoute()->setMode(ASAPI_Mode_RAW);
_ASRoute()->GoodSay(false);

// clear redis cache
//_ASRedis()->flush();

// force set language
//_I18n()->setLang($_GET['i18n'] ?? $_SESSION['i18n'] ?? 'zh-CN' , true );
