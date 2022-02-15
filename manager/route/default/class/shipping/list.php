<?php

use APS\ASAPI;
use APS\Encrypt;

$website->requireLogin('manager/login');
$website->requireGroupLevel(80000,'manager/insufficient');
$website->requireGroupCharacter(['super','manager','editor'],'manager/insufficient');

$website->params['itemClass'] = 'APS\CommerceShipping';
$website->params['filters']   = \APS\Filter::purify( $website->params, \APS\CommerceShipping::filterFields );

$callResult = ASAPI::systemInit( 'manager\itemList', $website->params, $website->user )->run() ;

$website->setTitle('Shipping');
$website->setMenuActive(['commerce','shipping',$website->params['status']??'all'=='trash' ? 'shippingListTrash' : 'shippingList']);

$website->setSubData('shippingList', $callResult->isSucceed() ? $callResult->getContent()['list'] : null );

$nav = $callResult->getContent()['nav'];

$website->setSubData('pager', $website->structPager( $nav['page'],$nav['size'] , $nav['total'], $website->params));

$website->setSubData('random', Encrypt::shortId(8));

$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$website->appendTemplateByFile(THEME_DIR.'common/sidebar.html');
$website->appendTemplateByFile(THEME_DIR.'common/topbar.html');

$website->appendTemplateByFile(THEME_DIR.'class/shipping/list.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer.html');

$website->rend();
