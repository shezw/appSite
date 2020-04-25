<?php

session_start();

\APS\User::common()->removeFromSession((getConfig('id','MANAGER') ?? 'APPSITE') . '_m');

$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$website->appendTemplateByFile(THEME_DIR.'page/logout.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer.html');

$logoutJS = <<<EOD

Aps.user.logout();

EOD;

$website->setSubData('customJS', $logoutJS);

$website->rend();
