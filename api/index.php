<?php
/**
 * 网络接口
 * API
 *
 * @link https://appsite.cn
 * @author Sprite Shur  https://shezw.com  hello@shezw.com
 * @copyright shezw.com
 * @version 2.0
 */

use APS\User;

include_once dirname(__DIR__).'/server/autoload.php';

//define('API_DIR',__DIR__);


//$a = \APS\JoinParams::common('APS\UserInfo')->asSubData('info');
//var_dump($a);

//var_dump(_ASRedis());
//_ASRedis()->cache('test','t123',100);
//
//
//var_dump(_ASRedis()->read('test'));

//_I18n()->loadDictionary(true);

//$a = \APS\ASAPI::systemInit( '\account\regist' );
//var_dump();
//\APS\Mixer::debug($a);

$user  = User::fromHeader();

$route = _ASRoute( getConfig('API_ROUTE_FORMAT') ?? 'api/namespace/class/id'  );

$route->runAPI( $user );

//$a = new \APS\SampleModel();
//
//var_dump($a);
//
//
//$b = new Sample();
//
//var_dump($b);
//var_dump('APS\UserInfo'::$table);


//$apiInstance = new $route['action'];
//$apiInstance->run();

//print $fileDir;

//$setting = _ASSetting();
//var_dump(getConfig('API_ROUTE_FORMAT'));
//APS\Mixer::debug(_ASRoute(getConfig('API_ROUTE_FORMAT')));

//APS\Mixer::debug(_ASRoute('api/action/test'));

//

//$a = new \APS\Category();
//$a->t();

//print gettype($a);


//var_dump(get_class($a) == 'APS\Category');

//--
//
//class test extends \APS\ASModel{
//
//    public function t(){
//        print_r( __CLASS__ );
//    }
//
//}
//
//$a = new test();
//$a->t();
