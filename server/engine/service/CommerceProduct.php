<?php

namespace APS;

/**
 * 电商 - 商品模块
 * CommerceProduct
 * @package APS\service
 */
class CommerceProduct extends ASModel{


    public static $table     = "commerce_product";  // 表
    public static $primaryid = "uid";     // 主字段

    public static $addFields = [
        'uid','categoryid','authorid',
        'type','mode','title','cover','gallery','video','link','tags','description','introduce','details',
        'price','sale','isvirtual','features','stock',
        'viewtimes','sort','featured','status',
        'createtime','lasttime',
    ];

    public static $updateFields = [
        'categoryid',
        'type','mode','title','cover','gallery','video','link','tags','description','introduce','details',
        'price','sale','isvirtual','features','stock',
        'viewtimes','sort','featured','status','createtime','lasttime'
    ];
    public static $detailFields  = ["*"];
    public static $overviewFields  = [
        'uid','categoryid','authorid',
        'type','mode','title','cover','gallery','video','link','tags','description','introduce','details',
        'price','sale','isvirtual','features','stock',
        'viewtimes','sort','featured','status','createtime','lasttime'
    ];
    public static $listFields  = [
        'uid','categoryid','authorid',
        'type','mode','title','cover','description','link','tags',
        'price','sale','isvirtual','stock',
        'viewtimes','sort','featured','status','createtime','lasttime'
    ];
    public static $countFilters  = [
        'uid','categoryid','authorid',
        'type','mode','title','keyword',
        'price','sale','isvirtual','stock',
        'viewtimes','sort','featured','status','createtime','lasttime'
    ];
    public static $depthStruct  = [
        'features'=>'ASJson',
        'isvirtual'=>'int',
        'price'=>'float',
        'sale'=>'float',
        'stock'=>'int',
        'gallery'=>'ASJson',
        'featured'=>'int',
        'createtime'=>'int',
        'lasttime'=>'int',
        'sort'=>'int'
    ];
    public static $searchFilters = ['title','description'];


}