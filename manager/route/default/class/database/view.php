<?php

/** @var Website $website */

use APS\Time;
use APS\Website;

$website->requireLogin('manager/login');
$website->requireGroupLevel(90000,'manager/insufficient');
$website->requireGroupCharacter('manager','manager/insufficient');

$website->setTitle('Database Management');
$website->setMenuActive(['setting','settingDatabase']);

$databases = _ASDB()->showTables()->getContent();
$redis     = _ASRedis()->isEnabled() ? _ASRedis()->info(true) : NULL;

for ($i=0; $i < count($databases); $i++) {
    $databases[$i]['CREATE_TIME']  = $databases[$i]['CREATE_TIME'] ? Time::fromString($databases[$i]['CREATE_TIME'])->customOutput("y-m-d H:s") : NULL;
    $databases[$i]['UPDATE_TIME']  = $databases[$i]['UPDATE_TIME'] ? Time::fromString($databases[$i]['UPDATE_TIME'])->customOutput("y-m-d H:s") : NULL;
}

$website->setSubData('databases', $databases );
$website->setSubData('redis', $redis );

$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$website->appendTemplateByFile(THEME_DIR.'common/sidebar.html');
$website->appendTemplateByFile(THEME_DIR.'common/topbar.html');

$website->appendTemplateByFile(THEME_DIR.'class/database/view.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer.html');

$website->rend();
