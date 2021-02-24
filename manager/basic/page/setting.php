<?php

$website->requireUser('manager/login');
$website->requireGroupLevel(40000,'manager/insufficient');
$website->requireGroupCharacter('manager','manager/insufficient');

$detail = $website->user->detail;
//var_dump($detail);
$website->setTitle('My account');
$website->setSubData('detail',$detail);
$website->setSubData('random',\APS\Encrypt::shortId(8));

$customJS = "	
    Aps.uploader.init(
        'image',
        {
            type:'image',
            selector:'imagePreview',
            container:'uploadContainer',
            FileUploaded:Aps.uploader.mediaUploaded,
        },
        {preview:'#imagePreview',input:'#image'});	

    Aps.former.watch('.a-field.dynamic');
";

$website->setSubData('customJS',$customJS);


$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$website->setMenuActive(['content','article']);
$website->appendTemplateByFile(THEME_DIR.'common/sidebar.html');
$website->blendMenuAccessByFile(SITE_DIR.'basic/menu/sidebar.php');

$website->appendTemplateByFile(THEME_DIR.'page/setting.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer/editor.html');

$website->rend();
