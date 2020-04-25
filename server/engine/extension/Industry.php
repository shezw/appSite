<?php

namespace APS;

/**
 * 行业
 * Industry
 * @package APS
 */
class Industry extends ASModel{

    public static $table     = "item_industry";
    public static $primaryid = "industryid";
    public static $addFields  = [
        'industryid','parentid',
        'title','cover','description','level',
        'sort','featured','status',
    ];
    public static $updateFields  = [
        'industryid','parentid',
        'title','cover','description','level',
        'sort','featured','status',
    ];
    public static $detailFields  = [
        'industryid','parentid',
        'title','cover','description','level',
        'sort','featured','status',
        'createtime','lasttime',
    ];
    public static $overviewFields  = [
        'industryid','parentid',
        'title','cover','description','level',
        'sort','featured','status',
        'createtime','lasttime',
    ];
    public static $listFields  = [
        'industryid','parentid',
        'title','cover','description','level',
        'sort','featured','status',
        'createtime','lasttime',
    ];
    public static $countFilters  = [
        'industryid','parentid',
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