<?php

use APS\Encrypt;

$website->requireLogin('manager/login');
$website->requireGroupLevel(40000,'manager/insufficient');
$website->requireGroupCharacter('manager','manager/insufficient');

$detail = $website->user->detail;

$website->setTitle('My account');
$website->setSubData('detail',$detail);
$website->setSubData('random', Encrypt::shortId(8));

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

$website->appendTemplateByFile(THEME_DIR.'common/sidebar.html');
$website->appendTemplateByFile(THEME_DIR.'common/topbar.html');

$website->appendTemplateByFile(THEME_DIR.'page/profile.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer-editor.html');

$website->rend();
