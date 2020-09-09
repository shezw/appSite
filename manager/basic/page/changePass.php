<?php

$website->requireUser('manager/login');
$website->requireGroupLevel(40000,'manager/insufficient');
$website->requireGroupCharacter('manager','manager/insufficient');

$detail = $website->user->detail;
//var_dump($detail);
$website->setTitle('Change my password');
$website->setSubData('detail',$detail);
$website->setSubData('random',\APS\Encrypt::shortId(8));

$customJS = "
";

$website->setSubData('customJS',$customJS);


$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$website->setMenuActive(['content','article']);
$website->appendTemplateByFile(THEME_DIR.'common/sidebar.html');
$website->blendMenuAccessByFile(SITE_DIR.'basic/menu/sidebar.php');

$website->appendTemplateByFile(THEME_DIR.'page/changePass.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer/editor.html');

$website->rend();
