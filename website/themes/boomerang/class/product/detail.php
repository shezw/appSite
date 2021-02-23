<?php
/**
 * 预览
 * preview.php
 */

$detail = \APS\CommerceProduct::common()->detail($website->route['id'])->getContent();

$website->setSubData('detail',$detail);
$website->setSubData('random',\APS\Encrypt::shortId(8));

$website->appendTemplateByFile(THEME_DIR.'common/header.html');
$website->appendTemplateByFile(THEME_DIR.'class/product/detail.html');
$website->appendTemplateByFile(THEME_DIR.'common/footer.html');

$website->setSubData('customFooter','');
$website->setSubData('customJS',"");

$website->rend();
