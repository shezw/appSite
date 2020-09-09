<?php

namespace APS;

/**
 * 一个模型的示范类
 * A Model Class sample
 * @package APS\custom\model
 */
class CommerceShipping extends ASModel{

    public static $table = 'commerce_shipping';
    public static $primaryid = 'uid';
    public static $addFields = [
		'uid','title','cover','description','amount','details','status','createtime','sort','featured'
    ];
    public static $updateFields = [
		'uid','title','cover','description','amount','details','status','createtime','sort','featured'
    ];
    public static $detailFields = '*';
    public static $overviewFields = [
		'uid','title','cover','description','amount','details','status','createtime','sort','featured'
    ];
    public static $countFilters = [
		'uid','amount','status','createtime','sort','featured'
    ];
    public static $depthStruct = [
        'amount'=>'double',
        'createtime'=>'int',
        'lasttime'=>'int',
        'sort'=>'int',
        'featured'=>'int',
    ];

}