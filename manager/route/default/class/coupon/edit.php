<?php

$website->requireLogin('manager/login');
$website->requireGroupLevel(80000,'manager/insufficient');
$website->requireGroupCharacter('manager','manager/insufficient');

$detail = \APS\CommerceCoupon::common()->detail($website->route['id'])->getContent();

$website->setTitle('Edit');
$website->setMenuActive(['commerce','coupon']);

$website->setSubData('detail',$detail);
$website->setSubData('random',\APS\Encrypt::shortId(8));

$customJS = "

    Aps.former.watch('.a-field');
";

$website->setSubData('customJS',$customJS);

$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$website->appendTemplateByFile(THEME_DIR.'common/sidebar.html');
$website->appendTemplateByFile(THEME_DIR.'common/topbar.html');

$website->appendTemplateByFile(THEME_DIR.'class/coupon/edit.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer-editor.html');

$website->rend();
