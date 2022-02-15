<?php

use APS\Encrypt;

$website->requireLogin('manager/login');
$website->requireGroupLevel(40000,'manager/insufficient');
$website->requireGroupCharacter(['super','manager','editor'],'manager/insufficient');

$website->setSubData('random', Encrypt::shortId(8));
$website->setSubData('customFooter',"
");
$website->setSubData('customJS',"
");


$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$website->setMenuActive(['appsiteJS','appsiteJSGUI']);
$website->appendTemplateByFile(THEME_DIR.'common/sidebar.html');
$website->appendTemplateByFile(THEME_DIR.'common/topbar.html');

$website->appendTemplateByFile(THEME_DIR.'class/appsiteJS/gui.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer-editor.html');

$website->rend();
