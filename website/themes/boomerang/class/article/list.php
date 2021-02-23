<?php
/**
 * 预览
 * preview.php
 */
/** @var \APS\Website $website */

$page = $website->params['page'] ?? 1;
$size = $website->params['size'] ?? 12;

$articleCount = \APS\Article::common()->count([])->getContent();
$articles = \APS\Article::common()->joinList([],null,[\APS\JoinParams::init('\\APS\\User','userid')->get(\APS\User::$overviewFields)->equalTo('item_article.authorid')->asSubData('author')],$page,$size)->getContent();

$website->setSubData('articles',$articles);
$website->setSubData('random',\APS\Encrypt::shortId(8));

$website->setSubData('pager',$website->structPager($page,$size,$articleCount,$website->params));

$website->appendTemplateByFile(THEME_DIR.'common/header.html');
$website->appendTemplateByFile(THEME_DIR.'class/article/list.html');
$website->appendTemplateByFile(THEME_DIR.'common/footer.html');

$website->setSubData('customJS',"");

$website->rend();
