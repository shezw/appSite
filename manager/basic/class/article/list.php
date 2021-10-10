<?php

$website->requireUser('manager/login');
$website->requireGroupLevel(80000,'manager/insufficient');
$website->requireGroupCharacter(['super','manager','editor'],'manager/insufficient');

$website->params['itemClass'] = 'APS\Article';
$website->params['filters']   = \APS\Filter::purify( $website->params, \APS\Article::filterFields );

$callResult = \APS\ASAPI::systemInit( 'manager\itemList', $website->params, $website->user )->run() ;

$website->setTitle('Article List');

$website->setSubData('articleList', $callResult->isSucceed() ? $callResult->getContent()['list'] : null );

$nav = $callResult->getContent()['nav'];

$website->setSubData('pager', $website->structPager( $nav['page'],$nav['size'] , $nav['total'], $website->params));

$getCategory = \APS\Category::common()->list(['type'=>'article']);
if( $getCategory->isSucceed() ){

    $website->setSubData('categoryList',$getCategory->getContent());

}

$website->setSubData('random',\APS\Encrypt::shortId(8));


$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$website->setMenuActive(['content','article',$website->params['status']=='trash' ? 'articleListTrash' : 'articleList']);
$website->appendTemplateByFile(THEME_DIR.'common/sidebar.html');
$website->blendMenuAccessByFile(SITE_DIR.'basic/menu/sidebar.php');

$website->appendTemplateByFile(THEME_DIR.'class/article/list.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer.html');

$website->rend();
