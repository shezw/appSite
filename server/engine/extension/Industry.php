<?php

namespace APS;

/**
 * 行业
 * Industry
 * @package APS
 */
class Industry extends ASModel{

    public static $table     = "item_industry";
    public static $primaryid = "uid";
    public static $addFields  = [
        'uid','parentid',
        'title','cover','description','level',
        'sort','featured','status',
    ];
    public static $updateFields  = [
        'uid','parentid',
        'title','cover','description','level',
        'sort','featured','status',
    ];
    public static $detailFields  = [
        'uid','parentid',
        'title','cover','description','level',
        'sort','featured','status',
        'createtime','lasttime',
    ];
    public static $overviewFields  = [
        'uid','parentid',
        'title','cover','description','level',
        'sort','featured','status',
        'createtime','lasttime',
    ];
    public static $listFields  = [
        'uid','parentid',
        'title','cover','description','level',
        'sort','featured','status',
        'createtime','lasttime',
    ];
    public static $countFilters  = [
        'uid','parentid',
        'sort','featured','status',
        'createtime','lasttime',
    ];
    public static $depthStruct  = [
        'level'=>'int',
        'sort'=>'int',
        'featured'=>'int',
        'createtime'=>'int',
        'lasttime'=>'int',
    ];

}