<?php
/** @var \APS\Website $website */

$website->requireUser('manager/login');
$website->requireGroupLevel(80000,'manager/insufficient');
$website->requireGroupCharacter(['super','manager','editor'],'manager/insufficient');

$website->setTitle('Add category');
$website->setMenuActive(['content','category','categoryAdd']);
$website->setSubData('type',$website->params['type']??'product');
$website->setSubData('random',\APS\Encrypt::shortId(8));
$website->setSubData('customFooter',"
");
$website->setSubData('customJS',"
    Aps.uploader.init(
        'image',
        {
            type:'image',
            selector:'broseImage',
            container:'uploadContainer',
            FileUploaded:Aps.uploader.mediaUploaded,
        },
        {preview:'#imagePreview',input:'#image'});	

");


$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$website->appendTemplateByFile(THEME_DIR.'common/sidebar.html');
$website->appendTemplateByFile(THEME_DIR.'common/topbar.html');

$website->appendTemplateByFile(THEME_DIR.'class/category/add.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer-editor.html');

$website->blendMenuAccessByFile(SITE_DIR.'basic/menu/sidebar.php');
$website->rend();
