<?php

$website->requireUser('manager/login');
$website->requireGroupLevel(80000,'manager/insufficient');
$website->requireGroupCharacter(['super','manager','editor'],'manager/insufficient');

$website->params['itemClass'] = 'APS\CommerceShipping';
$website->params['filters']   = \APS\Filter::purify( $website->params, \APS\CommerceShipping::$countFilters );

$callResult = \APS\ASAPI::systemInit( 'manager\itemList', $website->params, $website->user )->run() ;

$website->setTitle('Shipping');

$website->setSubData('shippingList', $callResult->isSucceed() ? $callResult->getContent()['list'] : null );

$nav = $callResult->getContent()['nav'];

$website->setSubData('pager', $website->structPager( $nav['page'],$nav['size'] , $nav['total'], $website->params));

$website->setSubData('random',\APS\Encrypt::shortId(8));


$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$website->setMenuActive(['shipping',$website->params['status']=='trash' ? 'shippingListTrash' : 'shippingList']);
$website->appendTemplateByFile(THEME_DIR.'common/sidebar.html');
$website->blendMenuAccessByFile(SITE_DIR.'basic/menu/sidebar.php');

$website->appendTemplateByFile(THEME_DIR.'class/shipping/list.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer.html');

$website->rend();
