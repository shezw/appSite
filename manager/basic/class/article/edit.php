<?php

$website->requireUser('manager/login');
$website->requireGroupLevel(40000,'manager/insufficient');
$website->requireGroupCharacter('manager','manager/insufficient');
//var_dump($website);
$detail = \APS\Article::common()->detail($website->route['id'])->getContent();

$getCategory = \APS\Category::common()->list(['type'=>'article']);
if( $getCategory->isSucceed() ){

    $website->setSubData('categoryList',$getCategory->getContent());

}

$website->setTitle('Edit');
$website->setSubData('detail',$detail);
$website->setSubData('random',\APS\Encrypt::shortId(8));

$customJS = "
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
	Aps.uploader.galleryInit();

    VD('#categoryid').value( '{$detail['categoryid']}' ).trigger('change');
    VD('#type').value( '{$detail['type']}' ).trigger('change');
    VD('#featured').toggleAttr( 'checked' , {$detail['featured']} );

    Aps.former.watch('.a-field');
";

$website->setSubData('customJS',$customJS);


$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$website->setMenuActive(['content','article']);
$website->appendTemplateByFile(THEME_DIR.'common/sidebar.html');
$website->blendMenuAccessByFile(SITE_DIR.'basic/menu/sidebar.php');

$website->appendTemplateByFile(THEME_DIR.'class/article/edit.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer/editor.html');

$website->rend();
