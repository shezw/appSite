<?php
/** @var Website $website */

use APS\ASAPI;
use APS\Category;
use APS\Filter;
use APS\Website;

$website->requireLogin('manager/login');
$website->requireGroupLevel(80000,'manager/insufficient');
$website->requireGroupCharacter(['super','manager','editor'],'manager/insufficient');

$website->params['itemClass'] = APS\Category::class;
$website->params['filters']   = Filter::purify( $website->params, Category::filterFields );
$website->params['order'] = $website->params['order'] ?? 'featured DESC, sort DESC, createtime DESC';

$callResult = ASAPI::systemInit( manager\itemList::class, $website->params, $website->user )->run() ;

$website->setTitle('Categories');
$website->setMenuActive(['content','category']);

$website->setSubData('categoryList', $callResult->isSucceed() ? $callResult->getContent()['list'] : null );

$nav = $callResult->getContent()['nav'];

$website->setSubData('pager', $website->structPager( $nav['page'],$nav['size'] , $nav['total'], $website->params));

$website->setSubData('ValidTypes',[
    ['id'=>'product','description'=>i18n('product','type')],
    ['id'=>'article','description'=>i18n('article','type')],
    ['id'=>'media','description'=>i18n('media','type')],
]);

$website->setSubData('random',\APS\Encrypt::shortId(8));

$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$website->appendTemplateByFile(THEME_DIR.'common/sidebar.html');
$website->appendTemplateByFile(THEME_DIR.'common/topbar.html');

$website->appendTemplateByFile(THEME_DIR.'class/category/list.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer.html');

$website->rend();
