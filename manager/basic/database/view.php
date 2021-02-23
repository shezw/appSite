<?php

/** @var \APS\Website $website */

$website->requireUser('manager/login');
$website->requireGroupLevel(90000,'manager/insufficient');
$website->requireGroupCharacter('manager','manager/insufficient');

$databases = _ASDB()->showTables()->getContent();
$redis     = _ASRedis()->isEnabled() ? _ASRedis()->info(true) : NULL;

for ($i=0; $i < count($databases); $i++) {
    $databases[$i]['CREATE_TIME']  = $databases[$i]['CREATE_TIME'] ? \APS\Time::fromString($databases[$i]['CREATE_TIME'])->customOutput("y-m-d H:s") : NULL;
    $databases[$i]['UPDATE_TIME']  = $databases[$i]['UPDATE_TIME'] ? \APS\Time::fromString($databases[$i]['UPDATE_TIME'])->customOutput("y-m-d H:s") : NULL;
}

$website->setTitle('Database Management');
$website->setMenuActive(['setting','settingDatabase']);

$website->setSubData('databases', $databases );
$website->setSubData('redis', $redis );

$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$website->appendTemplateByFile(THEME_DIR.'common/topbar.html');
$website->appendTemplateByFile(THEME_DIR.'common/sidebar.html');

$website->appendTemplateByFile(THEME_DIR.'class/database/view.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer.html');

$website->blendMenuAccessByFile(SITE_DIR.'basic/menu/sidebar.php');
$website->rend();