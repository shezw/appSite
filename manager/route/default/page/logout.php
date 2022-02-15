<?php

use APS\Management;
use APS\User;

/** @var Management $website */

if (!isset($_SESSION)) { session_start(); }

User::common()->removeFromSession(getConfig('id',RouteScopeManagement) ?? ManagementDefaultID );

$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$website->appendTemplateByFile(THEME_DIR.'page/logout.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer.html');

$logoutJS = <<<EOD

Aps.user.logout();

EOD;

$website->setSubData('customJS', $logoutJS);

$website->rend();
