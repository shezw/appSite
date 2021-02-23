<?php
/** @var \APS\Website $website */

$website->requireUser('manager/login');
$website->requireGroupLevel(80000,'manager/insufficient');
$website->requireGroupCharacter(['super','manager','editor'],'manager/insufficient');

$website->params['itemClass'] = 'APS\Category';
$website->params['filters']   = \APS\Filter::purify( $website->params, \APS\Category::$countFilters );
$website->params['order'] = $website->params['order'] ?? 'featured DESC, sort DESC, createtime DESC';

$callResult = \APS\ASAPI::systemInit( 'manager\itemList', $website->params, $website->user )->run() ;

$website->setTitle('Categories');
$website->setMenuActive(['content','category']);

$website->setSubData('categoryList', $callResult->isSucceed() ? $callResult->getContent()['list'] : null );

$nav = $callResult->getContent()['nav'];

$website->setSubData('pager', $website->structPager( $nav['page'],$nav['size'] , $nav['total'], $website->params));

$website->setSubData('random',\APS\Encrypt::shortId(8));

$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$website->appendTemplateByFile(THEME_DIR.'common/sidebar.html');
$website->appendTemplateByFile(THEME_DIR.'common/topbar.html');

$website->appendTemplateByFile(THEME_DIR.'class/category/list.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer.html');

$website->blendMenuAccessByFile(SITE_DIR . 'basic/menu/sidebar.php');

$website->rend();
