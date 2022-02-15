<?php

use APS\ASAPI;
use APS\CommerceOrder;
use APS\Encrypt;
use APS\Filter;
use manager\itemList;

$website->requireLogin('manager/login');
$website->requireGroupLevel(80000,'manager/insufficient');
$website->requireGroupCharacter(['super','manager','editor'],'manager/insufficient');

$website->setMenuActive(['order']);

$website->params['itemClass'] = 'APS\CommerceOrder';
$website->params['filters']   = Filter::purify( $website->params, CommerceOrder::filterFields );

$callResult = ASAPI::systemInit( itemList::class, $website->params, $website->user )->run() ;

$website->setTitle('Order');

$website->setSubData('orderList', $callResult->isSucceed() ? $callResult->getContent()['list'] : null );

$nav = $callResult->getContent()['nav'];

$website->setSubData('pager', $website->structPager( $nav['page'],$nav['size'] , $nav['total'], $website->params));

$website->setSubData('random', Encrypt::shortId(8));

$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$website->appendTemplateByFile(THEME_DIR.'common/sidebar.html');
$website->appendTemplateByFile(THEME_DIR.'common/topbar.html');

$website->appendTemplateByFile(THEME_DIR.'class/order/list.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer.html');

$website->rend();
