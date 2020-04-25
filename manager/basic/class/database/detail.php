<?php

$website->requireUser('manager/login');
$website->requireGroupLevel(90000,'manager/insufficient');
$website->requireGroupCharacter('manager','manager/insufficient');

$tableColumns = _ASDB()->showColumns($website->params['table'])->getContent();

$website->setTitle('Table Detail');

$website->setSubData('tableName',$website->params['comment']);
$website->setSubData('tableColumns', $tableColumns );

$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$website->appendTemplateByFile(THEME_DIR.'common/modal/styleCustomize.html');
$website->appendTemplateByFile(THEME_DIR.'common/modal/search.html');
$website->appendTemplateByFile(THEME_DIR.'common/modal/notification.html');

$website->appendTemplateByFile(THEME_DIR.'common/sidebar.html');
$website->blendMenuAccessByFile(SITE_DIR.'basic/menu/sidebar.php');
$website->setMenuActive(['setting','settingDatabase']);

$website->appendTemplateByFile(THEME_DIR.'class/database/detail.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer.html');

$website->rend();
