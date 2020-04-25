<?php
/**
 * Description
 * welcome.php
 */

$loginParams = \APS\Filter::purify( $website->params, ['account','password'] );

$loginAPI    = \APS\ASAPI::systemInit( '\account\passwordLogin', $loginParams );
$userLogin   = $loginAPI->run();

if ( $userLogin->isSucceed() ){

    $_SESSION['banLogin'] = null;

    $loginInfo = $userLogin->getContent();

    $user = \APS\User::shared( $loginInfo['userid'],$loginInfo['token'],$loginInfo['scope'] );
    $user->toSession( (getConfig('id','MANAGER') ?? 'APPSITE') . '_m' );

    $detail = $user->fullDetail();
    $userInfo = $detail->getContent();
    $userInfo['avatar'] = $userInfo['avatar'] ?? getConfig('defaultAvatar','WEBSITE');
    $website->setSubData('userInfo',$userInfo);


    $website->setSubData('customJS',"

    Aps.user.setProperty('userid','{$loginInfo['userid']}');
    Aps.user.setProperty('token','{$loginInfo['token']}');
    Aps.user.setProperty('scope','{$loginInfo['scope']}');
    Aps.user.setProperty('tokenexpire',{$loginInfo['expire']});
    
    ");

    $website->appendTemplateByFile(THEME_DIR.'page/welcome.html');

}else{

    session_start();
    $_SESSION['loginError'] = $userLogin->toArray();
    $website->redirectTo('manager/login');
}


$website->appendTemplateByFile(THEME_DIR.'common/header.html');
$website->appendTemplateByFile(THEME_DIR.'common/footer.html');

$website->rend();

//var_dump($website->params);