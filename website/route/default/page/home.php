<?php
/**
 * 首页
 * home.php
 */

use APS\Article;
use APS\DBConditions;
use APS\Website;

/** @var Website $website */
$website->setSubData('articles', Article::common()->list(DBConditions::init()->where('status')->equal(Status_Enabled),1,6,'featured DESC, sort DESC, createtime DESC')->getContent() );

$website->appendTemplateByFile(THEME_DIR.'common/header.html');
$website->appendTemplateByFile(THEME_DIR.'page/home.html');
$website->appendTemplateByFile(THEME_DIR.'common/footer.html');

$website->rend();
