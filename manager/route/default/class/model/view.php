<?php

/** @var Website $website */

use APS\Website;

$website->requireLogin('manager/login');
$website->requireGroupLevel(90000,'manager/insufficient');
$website->requireGroupCharacter('manager','manager/insufficient');

$customModels = [];
$customList = scandir(CUSTOM_DIR.'model/');
foreach ( $customList as $i => $name ){

    if( !strstr($name,'.php') ){ continue; }
    $className = str_replace( '.php','', $name );

    if( !class_exists($className) ){ continue; }
    if( !isset(class_parents($className)[\APS\ASModel::class]) ){ continue; }

    $customModels[] = [
        'name'=> $className,
        'comment'=>$className::comment,
        'table'=>$className::table,
        'struct'=>$className::tableStruct,
        'tableExist'=> _ASDB()->exist( null !== $className::table ? $className::table : '' )
    ];
}

$systemModels = [];

foreach ( DefaultModels as $i => $className ){

    $systemModels[] = [
        'name'=> $className,
        'comment'=>$className::comment,
        'table'=>$className::table,
        'struct'=>$className::tableStruct,
        'tableExist'=> _ASDB()->exist( null !== $className::table ? $className::table : '' )
    ];
}

//var_dump(scandir(CUSTOM_DIR.'model/'));

//$databases = _ASDB()->showTables()->getContent();
//$redis     = _ASRedis()->isEnabled() ? _ASRedis()->info(true) : NULL;
//
//for ($i=0; $i < count($databases); $i++) {
//    $databases[$i]['CREATE_TIME']  = $databases[$i]['CREATE_TIME'] ? \APS\Time::fromString($databases[$i]['CREATE_TIME'])->customOutput("y-m-d H:s") : NULL;
//    $databases[$i]['UPDATE_TIME']  = $databases[$i]['UPDATE_TIME'] ? \APS\Time::fromString($databases[$i]['UPDATE_TIME'])->customOutput("y-m-d H:s") : NULL;
//}

//var_dump($customModels);


$website->setTitle('Models Management');
$website->setMenuActive(['setting','settingModel']);

$website->setSubData('systemModels', $systemModels );
$website->setSubData('customModels', $customModels );

$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$website->appendTemplateByFile(THEME_DIR.'common/sidebar.html');
$website->appendTemplateByFile(THEME_DIR.'common/topbar.html');

$website->appendTemplateByFile(THEME_DIR.'class/model/view.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer.html');

$website->rend();
