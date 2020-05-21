<?php

$website->requireUser('manager/login');
$website->requireGroupLevel(80000,'manager/insufficient');
$website->requireGroupCharacter(['super','manager','editor'],'manager/insufficient');

$getCategory = \APS\Category::common()->list(['type'=>'article']);
if( $getCategory->isSucceed() ){

    $website->setSubData('categoryList',$getCategory->getContent());

}

$website->setSubData('random',\APS\Encrypt::shortId(8));
$website->setSubData('customFooter',"
");
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

$website->setMenuActive(['content','article','articleAdd']);
$website->appendTemplateByFile(THEME_DIR.'common/sidebar.html');
$website->blendMenuAccessByFile(SITE_DIR.'basic/menu/sidebar.php');

$website->appendTemplateByFile(THEME_DIR.'class/article/add.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer/editor.html');

$website->rend();
