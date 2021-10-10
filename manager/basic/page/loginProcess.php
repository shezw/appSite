<?php
/**
 * Description
 * welcome.php
 */

/** @var Website $website */

use APS\ASAPI;
use APS\Filter;
use APS\User;
use APS\Website;

$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$loginParams = Filter::purify( $website->params, ['account','password'] );

$loginAPI    = ASAPI::systemInit( account\passwordLogin::class, $loginParams );
$userLogin   = $loginAPI->run();

if ( $userLogin->isSucceed() ){

    $_SESSION['banLogin'] = null;

    $loginInfo = $userLogin->getContent();

    $user = User::shared( $loginInfo['userid'],$loginInfo['token'],$loginInfo['scope'] );
    $user->toSession( getConfig('id',RouteScopeManagement) ?? ManagementDefaultID );

    $detail = $user->fullDetail();
    $userInfo = $detail->getContent();
    $userInfo['avatar'] = $userInfo['avatar'] ?? getConfig('defaultAvatar',RouteScopeWebsite);

//    var_dump($userInfo);

    $website->setSubData('userInfo',$userInfo);

    $website->setSubData('customJS',"

    Aps.user.setProperty('userid','{$loginInfo['userid']}');
    Aps.user.setProperty('token','{$loginInfo['token']}');
    Aps.user.setProperty('scope','{$loginInfo['scope']}');
    Aps.user.setProperty('tokenexpire',{$loginInfo['expire']});
    
    ");

    $website->appendTemplateByFile(THEME_DIR.'page/welcome.html');

}else{

    if (!isset($_SESSION)) { session_start(); }

    $_SESSION['loginError'] = $userLogin->toArray();
    $website->redirectTo('manager/login');
}


$website->appendTemplateByFile(THEME_DIR.'common/footer.html');

$website->rend();
