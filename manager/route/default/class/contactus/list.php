<?php

use APS\ASAPI;
use APS\Filter;

$website->requireLogin('manager/login');
$website->requireGroupLevel(80000,'manager/insufficient');
$website->requireGroupCharacter(['super','manager','editor'],'manager/insufficient');

$website->params['itemClass'] = 'APS\MessageNotification';
$website->params['filters']   = Filter::purify( $website->params, \APS\MessageNotification::filterFields );

$website->params['filters']['linktype'] = 'contactus';

$callResult = ASAPI::systemInit( 'manager\itemList', $website->params, $website->user )->run() ;

$website->setTitle('Contact Us Messages');
$website->setMenuActive(['contactus']);

$website->setSubData('messageList', $callResult->isSucceed() ? $callResult->getContent()['list'] : null );

$nav = $callResult->getContent()['nav'];

$website->setSubData('pager', $website->structPager( $nav['page'],$nav['size'] , $nav['total'], $website->params));

$website->setSubData('random',\APS\Encrypt::shortId(8));


$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$website->appendTemplateByFile(THEME_DIR.'common/sidebar.html');
$website->appendTemplateByFile(THEME_DIR.'common/topbar.html');

$website->appendTemplateByFile(THEME_DIR.'class/contactus/list.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer.html');

$website->rend();
