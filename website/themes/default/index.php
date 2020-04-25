<?php
/**
 * Default Theme of AppSite Website
 * index.php

    路由格式 Route Format
    class/action/id

    常量
    THEME_DIR
    PAGE_DIR
    CLASS_DIR
    COMMON_DIR

    基础网站变量
    $website
    - route     路由
    - user      用户对象
    - querys    请求字符串
    - userData  用户基础数据
 *
 */


if( isset($website->route['action']) ) {  # Class Action
    $filePath = THEME_DIR.'class/'.$website->route['class'].'/'.$website->route['action'].'.php';
}else if( isset($website->route['class']) ){  # Page
    $filePath = THEME_DIR.'page/'.$website->route['class'].'.php';
}else{ # Home
    $filePath = THEME_DIR.'page/home.php';
}

if(file_exists($filePath)){
    include($filePath);
}else{
    file_exists(THEME_DIR.'page/404.php') && $website->to404();
    APS\Mixer::debug("<h1>404 Page Not found.</h1><p>This page not validable or not exists.</p><a href='/'>Back Home</a>");
}

