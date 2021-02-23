<?php
/**
 * 首页
 * home.php
 */

$website->setSubData('articles', \APS\Article::common()->list([],1,6,'featured DESC, sort DESC, createtime DESC')->getContent() );

$website->appendTemplateByFile(THEME_DIR.'common/header.html');
$website->appendTemplateByFile(THEME_DIR.'page/home.html');
$website->appendTemplateByFile(THEME_DIR.'common/footer.html');

$website->rend();
