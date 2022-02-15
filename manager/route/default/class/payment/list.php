<?php

use APS\CommercePayment;
use APS\Encrypt;
use APS\Filter;
use manager\itemList;

$website->requireLogin('manager/login');
$website->requireGroupLevel(80000,'manager/insufficient');
$website->requireGroupCharacter(['super','manager','editor'],'manager/insufficient');

$website->params['itemClass'] = CommercePayment::class;
$website->params['filters']   = Filter::purify( $website->params, CommercePayment::filterFields );

$callResult = \APS\ASAPI::systemInit( itemList::class, $website->params, $website->user )->run() ;

$website->setTitle('Payment');
$website->setMenuActive(['commerce','payment']);

$website->setSubData('list', $callResult->isSucceed() ? $callResult->getContent()['list'] : null );

$nav = $callResult->getContent()['nav'];

$website->setSubData('pager', $website->structPager( $nav['page'],$nav['size'] , $nav['total'], $website->params));

$website->setSubData('random', Encrypt::shortId(8));


$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$website->appendTemplateByFile(THEME_DIR.'common/sidebar.html');
$website->appendTemplateByFile(THEME_DIR.'common/topbar.html');

$website->appendTemplateByFile(THEME_DIR.'class/payment/list.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer.html');

$website->rend();
