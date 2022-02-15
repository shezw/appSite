<?php

use APS\Encrypt;

$website->requireLogin('manager/login');
$website->requireGroupLevel(40000,'manager/insufficient');
$website->requireGroupCharacter('manager','manager/insufficient');

$detail = $website->user->detail;
$website->setMenuActive(['dashboard']);


$website->setTitle('Change my password');
$website->setSubData('detail',$detail);
$website->setSubData('random', Encrypt::shortId(8));

$customJS = "
";

$website->setSubData('customJS',$customJS);


$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$website->appendTemplateByFile(THEME_DIR.'common/sidebar.html');
$website->appendTemplateByFile(THEME_DIR.'common/topbar.html');

$website->appendTemplateByFile(THEME_DIR.'page/changePass.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer-editor.html');

$website->rend();
