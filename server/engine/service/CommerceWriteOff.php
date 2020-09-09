<?php

namespace APS;

/**
 * 电商 - 核销
 * CommerceWriteOff
 * @package APS
 */
class CommerceWriteOff extends ASModel
{

    public static $table     = "commerce_writeoff";  // 表
    public static $primaryid = "uid";     // 主字段
    public static $addFields  = [
        'uid',
        'orderid',
        'targetid',
        'itemid',
        'userid',
        'status',
    ];
    public static $updateFields  = [
        'status',
    ];
    public static $detailFields  = ["*"];
    public static $overviewFields  = [
        'uid',
        'orderid',
        'targetid',
        'itemid',
        'userid',
        'status',
        'createtime',
        'lasttime',
        'sort',
        'featured',
    ];
    public static $listFields  = [
        'uid',
        'orderid',
        'targetid',
        'itemid',
        'userid',
        'status',
        'createtime',
        'lasttime',
        'sort',
        'featured',

    ];
    public static $countFilters  = [
        'uid',
        'orderid',
        'targetid',
        'itemid',
        'userid',
        'status',
        'createtime',
        'lasttime',
        'sort',
        'featured',

    ];
    public static $depthStruct  = [
        'createtime'=>'int',
        'lasttime'=>'int',
        'sort'=>'int',
        'featured'=>'int',
    ];


}