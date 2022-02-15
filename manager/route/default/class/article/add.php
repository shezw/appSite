<?php

use APS\Article;
use APS\Category;
use APS\Encrypt;
use APS\Management;

/** @var Management $website */

$website->requireLogin('manager/login');
$website->requireGroupLevel(80000,'manager/insufficient');
$website->requireGroupCharacter(['super','manager','editor'],'manager/insufficient');
$website->setMenuActive(['content','article','articleAdd']);

$getCategory = Category::common()->listByArray(['type'=> Article::class]);
if( $getCategory->isSucceed() ){

    $website->setSubData('categoryList',$getCategory->getContent());

}

$website->setSubData('random', Encrypt::shortId(8));
$website->setSubData('customFooter',"");
$website->setSubData('customJS',"
$('#summerNote').summernote();
Aps.uploader.initSummernoteUploader('.note-editor');
	
    Aps.uploader.init(
        'image',
        {
            type:'image',
            selector:'broseImage',
            container:'uploadContainer',
            FileUploaded:Aps.uploader.mediaUploaded,
        },
        {preview:'#imagePreview',input:'#image'});	

    Aps.uploader.init(
        'audio',
        {
            progress:1,
            type:'audio',
            selector:'brosweAudio',
            fileList:'#audioList',
            uploadBtn:'#uploadAudio',
            container:'audioContainer',
            FileUploaded:Aps.uploader.mediaUploaded,
        },
        {preview:'#audioPreview',input:'#audio'});

	Aps.uploader.init(
		'video',
		{
			progress:1,
			type:'video',
			selector:'brosweVideo',
			fileList:'#videoList',
			uploadBtn:'#uploadVideo',
			container:'videoContainer',
			FileUploaded:Aps.uploader.mediaUploaded,
		},
		{preview:'#videoPreview',input:'#video'});

	Aps.uploader.init(
		'gallery',
		{
			multi:true,
			progress:1,
			type:'image',
			selector:'brosweFiles',
			container:'galleryContainer',
			fileList:'#fileList',
			uploadBtn:'#uploadFiles',
			FileUploaded:Aps.uploader.galleryUploaded,
		},
		{galleryContainer:'#galleryList',input:'#gallery'});

    Aps.former.switchField('.a-field-switch-selector');

");


$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$website->appendTemplateByFile(THEME_DIR.'common/sidebar.html');
$website->appendTemplateByFile(THEME_DIR.'common/topbar.html');

$website->appendTemplateByFile(THEME_DIR.'class/article/add.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer-editor.html');

$website->rend();
