<?php

$website->requireLogin('manager/login');
$website->requireGroupLevel(80000,'manager/insufficient');
$website->requireGroupCharacter(['super','manager','editor'],'manager/insufficient');

$website->setMenuActive(['commerce','coupon','couponAdd']);

$website->setSubData('random',\APS\Encrypt::shortId(8));
$website->setSubData('customFooter',"
");
$website->setSubData('customJS',"
");

$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$website->appendTemplateByFile(THEME_DIR.'common/sidebar.html');
$website->appendTemplateByFile(THEME_DIR.'common/topbar.html');

$website->appendTemplateByFile(THEME_DIR.'class/coupon/bulkAdd.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer-editor.html');

$website->rend();
