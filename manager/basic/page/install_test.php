<?php
/**
 * 安装支持
 * install.php
 */
/** @var Website $website */

use APS\ASDB;
use APS\ASResult;
use APS\ASSetting;
use APS\Encrypt;
use APS\File;
use APS\Mixer;
use APS\User;
use APS\Website;

$website->appendTemplateByFile(THEME_DIR.'common/header.html');
$website->setTitle('Installation');

////File::removeFile(SERVER_DIR.'config.php');
//
//$environments = [];
//
//$phpVersion = floatval(phpversion());
//$versionOK = $phpVersion >= 7;
//
//$environments[] = [
//    'title'=>'PHP '.i18n('version'),
//    'least'=>'7.0',
//    'recommend'=>7.2,
//    'current'=>$phpVersion,
//    'status'=> $phpVersion >= 7 ? ($phpVersion >= 7.2 ? 'perfect' : 'good') : 'failed' ,
//    'comment'=>i18n( 'PHP_VERSION_INTRODUCE','manager' )
//];
//
//$redisOK = true;
//$environments[] = [
//    'title'=>'Redis '.i18n('extension'),
//    'least'=>i18n('none'),
//    'recommend'=>i18n('Install','manager'),
//    'current'=> i18n(class_exists('Redis') ? 'Installed' : 'NotInstall','manager'),
//    'status'=> class_exists('Redis') ? 'perfect' : 'ok',
//    'comment'=>i18n('REDIS_INTRODUCE','manager')
//];
//
//$mysqliOK = function_exists('mysqli_info');
//$environments[] = [
//    'title'  => 'mysqli',
//    'least'  => i18n('Install','manager'),
//    'current'=> i18n($mysqliOK ? 'Installed' : 'NotInstall','manager' ),
//    'status' => $mysqliOK ? 'perfect' : 'ok',
//];
//
//$curlOK = function_exists('curl_init');
//$environments[] = [
//    'title'=>'CURL '.i18n('extension'),
//    'least'=>i18n('Install','manager'),
//    'current'=> i18n($curlOK ? 'Installed' : 'NotEnabled', 'manager'),
//    'status'=> $curlOK ? 'perfect' : 'failed',
//];
//
//
//$website->setSubData('environments',$environments);
//$website->setSubData('canNext',( $versionOK && $redisOK && $mysqliOK && $curlOK ));
//
//$website->appendTemplateByFile(THEME_DIR.'page/install_env.html');



//$website->setSubData('db',$website->params);
//
//$DB = new ASDB($website->params['host'],$website->params['user'],$website->params['pass'],$website->params['base']);
//
//$DB_Connected = $DB->touch();
//
//$website->setSubData('version',$DB->getVersion());
//$website->setSubData('canNext',$DB_Connected);
//
//if( $DB_Connected ){
//    session_start();
//    $_SESSION['db'] = $website->params;
//}else{
//    $_SESSION['db'] = null;
//}
//
//$website->appendTemplateByFile(THEME_DIR.'page/install_linkDB.html');


//$canWrite = touch(SERVER_DIR.'touch.php');
//
//if( $canWrite ){
//    unlink(SERVER_DIR.'touch.php');
//}
//
//$website->setSubData('canWrite',$canWrite);
//$website->setSubData('RedisEnabled',class_exists('Redis'));
//$website->appendTemplateByFile(THEME_DIR.'page/install_setConfig.html');


//$website->appendTemplateByFile(THEME_DIR.'page/install_process.html');


//$i18nEnTemplate = file_get_contents(SERVER_DIR.'custom/localization/en-WW.sample.lang');
//$i18nCnTemplate = file_get_contents(SERVER_DIR.'custom/localization/zh-CN.sample.lang');
//$writeEnI18n = File::newFile( SERVER_DIR.'custom/localization/en-WW.lang',$i18nEnTemplate );
//$writeCnI18n = File::newFile( SERVER_DIR.'custom/localization/zh-CN.lang',$i18nCnTemplate );

//$network = new \APS\Network();
//$ad = $network->getJson('https://appsite.cn/api/Advertising/installSuccess');
//$website->setSubData('ad',$ad['content']);
//$website->appendTemplateByFile(THEME_DIR.'page/install_done.html');



$website->appendTemplateByFile(THEME_DIR.'common/footer.html');
$website->rend();
