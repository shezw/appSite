<?php
/**
 * 安装支持
 * install.php
 */

use APS\ASDB;
use APS\ASSetting;
use APS\Encrypt;
use APS\File;
use APS\Mixer;
use APS\User;

$installed = false;

$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$step = $_GET['step'] ?? 'env';

if (file_exists(SERVER_DIR.'config.php')){
    $installed = true;
    $step = 'done';
}

switch ( $step ){
    case 'env':

        File::removeFile(SERVER_DIR.'config.php');

        $environments = [];

        $phpVersion = floatval(phpversion());
        $versionOK = $phpVersion >= 7;

        $environments[] = [
            'title'=>'PHP '.i18n('version'),
            'least'=>'7.0',
            'recommend'=>7.2,
            'current'=>$phpVersion,
            'status'=> $phpVersion >= 7 ? ($phpVersion >= 7.2 ? 'perfect' : 'ok') : 'failed' ,
            'comment'=>i18n( 'PHP_VERSION_INTRODUCE','manager' )
        ];

        $redisOK = true;
        $environments[] = [
            'title'=>'Redis '.i18n('extension'),
            'least'=>i18n('none'),
            'recommend'=>i18n('Install','manager'),
            'current'=> i18n(class_exists('Redis') ? 'Installed' : 'NotInstall','manager'),
            'status'=> class_exists('Redis') ? 'perfect' : 'ok',
            'comment'=>i18n('REDIS_INTRODUCE','manager')
        ];

        $mysqliOK = function_exists('mysqli_info');
        $environments[] = [
            'title'  => 'mysqli',
            'least'  => i18n('Install','manager'),
            'current'=> i18n($mysqliOK ? 'Installed' : 'NotInstall','manager' ),
            'status' => $mysqliOK ? 'perfect' : 'ok',
        ];

        $curlOK = function_exists('curl_init');
        $environments[] = [
            'title'=>'CURL '.i18n('extension'),
            'least'=>i18n('Install','manager'),
            'current'=> i18n($curlOK ? 'Installed' : 'NotEnabled', 'manager'),
            'status'=> $curlOK ? 'perfect' : 'failed',
        ];


        $website->setSubData('environments',$environments);
        $website->setSubData('canNext',( $versionOK && $redisOK && $mysqliOK && $curlOK ));


        $website->appendTemplateByFile(THEME_DIR.'page/install_env.html');

    break;

    case 'linkDB':

        File::removeFile(SERVER_DIR.'config.php');

        $website->setSubData('db',$website->params);

        $DB = new ASDB($website->params['host'],$website->params['user'],$website->params['pass'],$website->params['base']);

        $DB_Connected = $DB->touch();

        $website->setSubData('version',$DB->getVersion());
        $website->setSubData('canNext',$DB_Connected);

        if( $DB_Connected ){
            session_start();
            $_SESSION['db'] = $website->params;
        }else{
            $_SESSION['db'] = null;
        }

        $website->appendTemplateByFile(THEME_DIR.'page/install_linkDB.html');

    break;

    case 'setConfig':

        File::removeFile(SERVER_DIR.'config.php');

        $canWrite = touch(SERVER_DIR.'touch.php');

        if( $canWrite ){
            unlink(SERVER_DIR.'touch.php');
        }

        $website->setSubData('canWrite',$canWrite);
        $website->setSubData('RedisEnabled',class_exists('Redis'));
        $website->appendTemplateByFile(THEME_DIR.'page/install_setConfig.html');

    break;

    case 'process':

        $website->appendTemplateByFile(THEME_DIR.'page/install_process.html');

        $configData     = [
            'db'=>$_SESSION['db'],
            'redis'=>[
                'host'=>$website->params['redishost'] ?? '127.0.0.1',
                'port'=>$website->params['redisport'] ?? 6379
            ],
            'site'=>[
                'path'=>$website->params['siteurl'],
                'api'=>$website->params['siteurl'].'api/',
                'website'=>$website->params['siteurl'],
                'static'=>$website->params['siteurl'].'website/static/',
            ],
            'server'=>[
                'ip'=>$website->params['serverip'],
            ],
            'DB_MODE'=> $website->params['dbmode'] == 'database' ? 'true' : 'false',
            'uid' => Encrypt::shortId(8)
        ];

    # 创建数据库
        _ASDB(new ASDB(
            $configData['db']['host'],
            $configData['db']['user'],
            $configData['db']['pass'],
            $configData['db']['base']
        ));

        $databaseStruct = require_once SERVER_DIR.'engine/databaseStruct.php';
        $createDatabase = _ASDB()->newDataStruct($databaseStruct,$configData['db']['base'])->getContent();
        $website->setSubData('databaseSuccess', checkMultipleResult($createDatabase) );
        $website->setSubData('databaseResult', getMultipleResultString($createDatabase));

        if( !$website->data['databaseSuccess'] ){
            _ASDB()->clearDataStruct( $databaseStruct );
            break;
        }

    # 初始化数据
        $databaseInitData = require_once SERVER_DIR.'engine/databaseInitData.php';
        $insertData = _ASDB()->autoInsertData($databaseInitData,$configData['db']['base'])->getContent();
        $website->setSubData('dataResult', getMultipleResultString($insertData));
        $website->setSubData('dataSuccess', checkMultipleResult($insertData) );

        if( !$website->data['dataSuccess'] ){
            break;
        }

    # 更新自定义配置项
        $customResult   = [];

        # 创建管理员账户
        $website->params['groupid'] = 900;
        $customResult[] = User::common()->add($website->params);
        $customResult[] = ASSetting::common()->set('MAIN_PATH',$configData['site']['path']);
        $customResult[] = ASSetting::common()->set('SITE_PATH',$configData['site']['website']);
        $customResult[] = ASSetting::common()->set('API_PATH',$configData['site']['api']);
        $customResult[] = ASSetting::common()->set('STATIC_PATH',$configData['site']['static']);
        $customResult[] = ASSetting::common()->set('SERVER_IP',$configData['server']['ip']);

        $customResult[] = ASSetting::common()->set('REDIS_HOST',$configData['redis']['host']);
        $customResult[] = ASSetting::common()->set('REDIS_PORT',$configData['redis']['port']);

        $customResult[] = ASSetting::common()->set('id',$configData['uid'],null,'MANAGER');
        $customResult[] = ASSetting::common()->set('id',$configData['uid'],null,'WEBSITE');

        $website->setSubData('customResult',getMultipleResultString($customResult));
        $website->setSubData('customSuccess',checkMultipleResult($customResult));


    # 写入CONFIG.PHP文件
        $configTemplate = file_get_contents(SERVER_DIR.'config.sample.php');
        $configFileContent =  Mixer::mix($configData,$configTemplate);
        $writeConfig = File::new( SERVER_DIR.'config.php', $configFileContent );

        $website->setSubData('configFileSaved',$writeConfig ? 'success' : 'failed');
        if( !$writeConfig ){
            $website->setSubData('config',htmlspecialchars($configFileContent));
        }

    # 写入config.manager.js, config.website.js 文件
        $configManagerJSTemplate = file_get_contents(SITE_DIR.'static/js/config.manager.sample.js');
        $configManagerJSFileContent = Mixer::mix($configData,$configManagerJSTemplate);
        $writeConfigManager = File::new( SITE_DIR.'static/js/config.manager.js', $configManagerJSFileContent );

        $website->setSubData('configManagerFileSaved',$writeConfig ? 'success' : 'failed');
        if( !$writeConfigManager ){
            $website->setSubData('configManager',htmlspecialchars($configManagerJSFileContent));
        }

        $configWebsiteJSTemplate = file_get_contents(SITE_DIR.'static/js/config.website.sample.js');
        $configWebsiteJSFileContent = Mixer::mix($configData,$configWebsiteJSTemplate);
        $writeConfigWebsite = File::new( SITE_DIR.'static/js/config.website.js', $configWebsiteJSFileContent );

        $website->setSubData('configWebsiteFileSaved',$writeConfig ? 'success' : 'failed');
        if( !$writeConfigWebsite ){
            $website->setSubData('configWebsite',htmlspecialchars($configWebsiteJSFileContent));
        }


        $website->setSubData('processSuccess', $website->data['configFileSaved'] == 'success' && $website->data['databaseSuccess'] && $website->data['dataSuccess'] && $website->data['customSuccess'] );

    break;

    case 'done':

//        $network = new \APS\Network();
//        $ad = $network->getJson('https://appsite.cn/api/Advertising/installSuccess');
//        $website->setSubData('ad',$ad['content']);

        $website->appendTemplateByFile(THEME_DIR.'page/install_done.html');

    break;

}

/**
 * 检测是否全部成功
 * checkMultipleResult
 * @param  \APS\ASResult[]  $results
 * @return bool
 */
function checkMultipleResult( array $results ){

    foreach ( $results as $i => $result ){
        if( !$result->isSucceed() ){
            return false;
        }
    }
    return true;
}

/**
 * 输出全部string
 * getMultipleResultString
 * @param  \APS\ASResult[] $results
 * @return string
 */
function getMultipleResultString( array $results ){

    $string = "";
    foreach ( $results as $i => $result ){
        $string .= $result->toString()."\n";
    }
    return $string;
}



$website->appendTemplateByFile(THEME_DIR.'common/footer.html');
$website->rend();
