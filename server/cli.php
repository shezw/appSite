<?php
/**
 * 命令行模式入口
 * CLI.php
 *
    使用getopt函数获取参数
    EG: php /folder/server/interval.php -f "actionName" --action action --params="loop:3"
 *
 */


ignore_user_abort(); // 后台运行
ini_set('date.timezone','Asia/Shanghai');
require_once __DIR__.'/autoload.php';


$shortOpts  = "";
$shortOpts .= "f:";  // Required value
$longOpts   = [
    "action:",       // Required value
    "params::",      // Optional value
];

$input = getopt($shortOpts, $longOpts);

