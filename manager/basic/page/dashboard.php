<?php

//var_dump($website);
$website->requireUser('manager/login');
$website->setTitle('Dashboard');

$website->blendMenuAccessByFile(SITE_DIR.'basic/menu/sidebar.php');
//$website->setSubData('menu',['operation'=>true,'operationUser'=>true]);

$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$website->appendTemplateByFile(THEME_DIR.'common/modal/styleCustomize.html');
$website->appendTemplateByFile(THEME_DIR.'common/modal/search.html');
$website->appendTemplateByFile(THEME_DIR.'common/modal/notification.html');

$website->appendTemplateByFile(THEME_DIR.'common/sidebar.html');
$website->setMenuActive(['dashboard']);

$website->appendTemplateByFile(THEME_DIR.'page/dashboard.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer.html');

//$website->setSubData('constants',$website->constants);
//$website->setSubData('userData',$website->userData);
//
//var_dump(   \APS\Encrypt::convertByteSize(mb_strlen($website->html_template)) );
//$start = microtime(true);
//
//    $test = \APS\Mixer::mix($website->data,$website->html_template);
//
//$end = microtime(true);
//
//var_dump($end-$start);

$website->rend();
