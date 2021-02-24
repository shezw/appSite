<?php

/** @var \APS\Website $website */

$website->requireUser('manager/login');
$website->requireGroupLevel(90000,'manager/insufficient');
$website->requireGroupCharacter('manager','manager/insufficient');

$tableColumns = _ASDB()->showColumns($website->params['table'])->getContent();

$website->setTitle('Table Detail');
$website->setMenuActive(['setting','settingDatabase']);

$website->setSubData('tableName',$website->params['comment']);
$website->setSubData('table',$website->params['table']);
$website->setSubData('tableColumns', $tableColumns );

$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$website->appendTemplateByFile(THEME_DIR.'common/topbar.html');
$website->appendTemplateByFile(THEME_DIR.'common/sidebar.html');

$website->appendTemplateByFile(THEME_DIR.'class/database/detail.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer.html');

$website->blendMenuAccessByFile(SITE_DIR.'basic/menu/sidebar.php');
$website->rend();
