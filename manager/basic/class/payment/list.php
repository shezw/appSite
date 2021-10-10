<?php

$website->requireUser('manager/login');
$website->requireGroupLevel(80000,'manager/insufficient');
$website->requireGroupCharacter(['super','manager','editor'],'manager/insufficient');

$website->params['itemClass'] = 'APS\CommercePayment';
$website->params['filters']   = \APS\Filter::purify( $website->params, \APS\CommercePayment::filterFields );

$callResult = \APS\ASAPI::systemInit( 'manager\itemList', $website->params, $website->user )->run() ;

$website->setTitle('Payment');

$website->setSubData('productList', $callResult->isSucceed() ? $callResult->getContent()['list'] : null );

$nav = $callResult->getContent()['nav'];

$website->setSubData('pager', $website->structPager( $nav['page'],$nav['size'] , $nav['total'], $website->params));

$website->setSubData('random',\APS\Encrypt::shortId(8));


$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$website->setMenuActive(['payment']);
$website->appendTemplateByFile(THEME_DIR.'common/sidebar.html');
$website->blendMenuAccessByFile(SITE_DIR.'basic/menu/sidebar.php');

$website->appendTemplateByFile(THEME_DIR.'class/payment/list.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer.html');

$website->rend();
