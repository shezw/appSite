<?php

namespace APS;

/**
 * 电商 - 库存模块
 * CommerceStock
 * @package APS\service
 */
class CommerceStock extends ASModel{


    public static $table     = "commerce_stock";  // 表
    public static $primaryid = "stockid";     // 主字段

    public static $addFields = [
        'stockid','productid','authorid',
        'type','mode','title','cover','description','stock',
        'sort','featured','status',
        'createtime','lasttime',
    ];

    public static $updateFields = [
        'stockid','productid','authorid',
        'type','mode','title','cover','description','stock',
        'sort','featured','status',
        'createtime','lasttime',
    ];
    public static $detailFields  = ["*"];
    public static $overviewFields  = [
        'stockid','productid','authorid',
        'type','mode','title','cover','description','stock',
        'sort','featured','status',
        'createtime','lasttime',
    ];
    public static $listFields  = [
        'stockid','productid','authorid',
        'type','mode','title','cover','description','stock',
        'sort','featured','status',
        'createtime','lasttime',
    ];
    public static $countFilters  = [
        'stockid','productid','authorid',
        'type','mode','title','cover','description','stock',
        'sort','featured','status',
        'createtime','lasttime',
    ];
    public static $depthStruct  = [
        'stock'=>'int',
        'featured'=>'int',
        'createtime'=>'int',
        'lasttime'=>'int',
        'sort'=>'int'
    ];
    public static $searchFilters = ['title','description'];


}