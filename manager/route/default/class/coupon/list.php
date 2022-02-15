<?php

/** @var Website $website */

use APS\ASAPI;
use APS\CommerceCoupon;
use APS\Filter;
use APS\Website;
use manager\itemList;

$website->requireLogin('manager/login');
$website->requireGroupLevel(80000,'manager/insufficient');
$website->requireGroupCharacter(['super','manager','editor'],'manager/insufficient');

$website->params['itemClass'] = CommerceCoupon::class;
$website->params['filters']   = Filter::purify( $website->params, CommerceCoupon::filterFields );

$callResult = ASAPI::systemInit( itemList::class, $website->params, $website->user )->run() ;

$website->setTitle('Coupon');
$website->setMenuActive(['commerce','coupon',$website->params['status']??'all'=='trash' ? 'couponListTrash' : 'couponList']);

$website->setSubData('list', $callResult->isSucceed() ? $callResult->getContent()['list'] : null );

$nav = $callResult->getContent()['nav'];

$website->setSubData('pager', $website->structPager( $nav['page'],$nav['size'] , $nav['total'], $website->params));

$website->setSubData('random',\APS\Encrypt::shortId(8));

$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$website->appendTemplateByFile(THEME_DIR.'common/sidebar.html');
$website->appendTemplateByFile(THEME_DIR.'common/topbar.html');

$website->appendTemplateByFile(THEME_DIR.'class/coupon/list.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer.html');

$website->rend();
