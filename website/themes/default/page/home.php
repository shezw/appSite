<?php
/**
 * 首页
 * home.php
 */

$website->appendTemplateByFile(THEME_DIR.'common/header.html');
$website->appendTemplateByFile(THEME_DIR.'page/home.html');
$website->appendTemplateByFile(THEME_DIR.'common/footer.html');

$website->rend();
