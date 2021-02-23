<?php

$website->requireUser('manager/login');
$website->requireGroupLevel(80000,'manager/insufficient');
$website->requireGroupCharacter(['super','manager','editor'],'manager/insufficient');

$website->setSubData('random',\APS\Encrypt::shortId(8));
$website->setSubData('customFooter',"
");
$website->setSubData('customJS',"
");


$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$website->setMenuActive(['coupon','couponAdd']);
$website->appendTemplateByFile(THEME_DIR.'common/sidebar.html');
$website->blendMenuAccessByFile(SITE_DIR.'basic/menu/sidebar.php');

$website->appendTemplateByFile(THEME_DIR.'class/coupon/bulkAdd.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer/editor.html');

$website->rend();
