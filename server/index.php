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

//\APS\UserGroup::common()->update(['menuaccess'=>['operation','setting','settingApiTest']],'300');

_ASRoute()->setMode('RAW');
_ASRoute()->GoodSay(false);

//_ASRedis()->flush();
//_I18n()->setLang($_GET['i18n'] ?? $_SESSION['i18n'] ?? 'zh-CN' , true );


//var_dump($_SERVER['SERVER_PORT']);
//var_dump(i18n('zh-CN','Sample_i18n_scope'));

//$a = new Sample();

//var_dump($a);

//var_dump(_I18n());

//class testAPI extends APS\ASAPI{
//
//    public $scope = 'ios';
//}
//
//$tapi = new testAPI();
//
//\APS\Mixer::debug( $tapi );

//var_dump($tapi);

//phpinfo();
//var_dump(_ASDB()->selectDB('macut'));
//var_dump(_ASDB()->touch());
//
//var_dump(class_exists('Redis'));
//print_r(_ASError());

//print_r(CONFIG);
//print getConfig("APP_NAME");
//print json_encode( _ASDB()->result );
//$i18n = _I18n("zh-CN");

//print "test sftp";
//_ASError()->debug(true);
//var_dump(_ASError());
//print_r($i18n);

//print i18n('500','statusCode');

//_ASRedis();
//$test = 0;


//print_r(_Setting());
//_I18n()->setLang("zh-CN");
//print_r (_ASRedis());
//print_r($GLOBALS);

//$t = new \APS\Time();
//$t = \APS\Time::fromString("2020-03-19");

//echo $t->humanityOutput();

//print $t->weekday();
//print $t->month();

//$a = new \APS\ASRedis();
//$db = _ASDB();
//$a->message(null);
//$a->feedback( 0, 'aaa' );

//$a = APS\User::shared();
//var_dump($a);
/* Init Common Object */

// $error  = APS\ASError::shared();
// $rds    = APS\ASRedis::shared();


// $databaseInitData = include "engine/databaseInitData.php";
// $databaseStruct = include "engine/databaseStruct.php";

// print(json_encode($a,256));

// class BPS extends APS\ASObject{
// 	protected $CLASSNAME = "bps";
// }

// $a = new BPS();
// $a->message([1]);
// print_r($a->feedback(null,null));
// print_r($a->message(null));
// print_r($a);

// $t = new TIME();

// print_r($t);
// phpinfo();
