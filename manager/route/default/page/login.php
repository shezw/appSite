<?php
/**
 * 登录
 * login.php
 */
/** @var Website $website */

use APS\Website;

//var_dump($website->user);
//var_dump($_SESSION);
$website->appendTemplateByFile(THEME_DIR.'common/header.html');

if( isset($_SESSION['banLogin']) && $_SESSION['banLogin'] > time() ){

    $openLoginTime = new \APS\Time( time() + 24*3600 );

    $website->setSubData('openLoginTime',$openLoginTime->formatOutput(TimeFormat_FullTime));
    $website->appendTemplateByFile(THEME_DIR.'page/ban.html');

}else{

    $_SESSION['banLogin'] = null;
    $website->appendTemplateByFile(THEME_DIR.'page/login.html');

    $maxLoginErrorTimes = getConfig('maxLoginErrorTimes') ?? 10;

    $website->setData([
        'title'=> getConfig('title',RouteScopeManagement),
        'description'=>getConfig('description',RouteScopeManagement),
        'logo'=>getConfig('logoUrl',RouteScopeManagement) ?? '/website/static/appsite/images/logo480.png'
    ]);

    $_SESSION['currentLoginErrorTimes'] = $_SESSION['currentLoginErrorTimes'] ?? 0;

    if ( isset($_SESSION['loginError'])){
        $website->setSubData('error',$_SESSION['loginError']);
        $_SESSION['currentLoginErrorTimes']++;
        if( $_SESSION['currentLoginErrorTimes'] > 3 ){
            $website->setSubData('maxErrorTimes',$maxLoginErrorTimes);
            $website->setSubData('errorTimes',$_SESSION['currentLoginErrorTimes']);
        }
        $_SESSION['loginError'] = null;

        if( $_SESSION['currentLoginErrorTimes'] >= $maxLoginErrorTimes ){
            $_SESSION['banLogin'] = time() + 24*3600;
        }
    }
}

//var_dump($website);


$website->appendTemplateByFile(THEME_DIR.'common/footer.html');

$website->rend();
