<?php

use APS\ASAPI;
use APS\Category;

$website->requireLogin('manager/login');
$website->requireGroupLevel(80000,'manager/insufficient');
$website->requireGroupCharacter(['super','manager','editor'],'manager/insufficient');

$website->params['itemClass'] = 'APS\Article';
$website->params['filters']   = \APS\Filter::purify( $website->params, \APS\Article::filterFields );

$callResult = ASAPI::systemInit( 'manager\itemList', $website->params, $website->user )->run() ;

$website->setTitle('Article List');
$website->setMenuActive(['content','article',$website->params['status']??'all'=='trash' ? 'articleListTrash' : 'articleList']);

$website->setSubData('list', $callResult->isSucceed() ? $callResult->getContent()['list'] : null );

$nav = $callResult->getContent()['nav'];

$website->setSubData('pager', $website->structPager( $nav['page'],$nav['size'] , $nav['total'], $website->params));

$getCategory = Category::common()->listByArray(['type'=>'article']);
if( $getCategory->isSucceed() ){

    $website->setSubData('categoryList',$getCategory->getContent());

}

$website->setSubData('random',\APS\Encrypt::shortId(8));

$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$website->appendTemplateByFile(THEME_DIR.'common/sidebar.html');
$website->appendTemplateByFile(THEME_DIR.'common/topbar.html');

$website->appendTemplateByFile(THEME_DIR.'class/article/list.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer.html');

$website->rend();
