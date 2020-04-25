<?php
/**
 * 自定义组件加载
 * autoload.php
 */


/**
 * 自定义类自动加载
 * Custom class autoload
 */
spl_autoload_register(function ($customClassName){

    $pathname   = __DIR__ . DIRECTORY_SEPARATOR;
    $filename   = str_replace('APS\\', '', $customClassName);

    if ( file_exists( "{$pathname}model/{$filename}.php" ) ){
        include "{$pathname}model/{$filename}.php";
        return true;
    }
    return false;
});

/**
 * 自动加载自定义本地化文件
 * Auto load custom localization file
 */
_I18n()->supplementWith( __DIR__.'/localization/' );
