<?php

use APS\CommercePayment;
use APS\Time;

$website->requireLogin('manager/login');
$website->requireGroupLevel(80000,'manager/insufficient');
$website->requireGroupCharacter(['super','manager','editor'],'manager/insufficient');

$website->setMenuActive(['order']);
$website->setTitle('Payment Detail');

$detail = CommercePayment::common()->detail($website->route['id'])->getContent();

$detail['status_'] = i18n($detail['status']);
$detail['createtime_'] = Time::common($detail['createtime'])->customOutput();
$detail['lasttime_'] = Time::common($detail['lasttime'])->customOutput();

$website->setSubData('detail',$detail);

$website->setSubData('random',\APS\Encrypt::shortId(8));

$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$website->appendTemplateByFile(THEME_DIR.'common/sidebar.html');

$website->appendTemplateByFile(THEME_DIR.'class/payment/detail.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer.html');

$website->rend();
