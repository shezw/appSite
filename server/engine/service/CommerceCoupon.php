<?php

namespace APS;

/**
 * 一个模型的示范类
 * A Model Class sample
 * @package APS\custom\model
 */
class CommerceCoupon extends ASModel{

    public static $table = 'commerce_coupon';
    public static $primaryid = 'couponid';
    public static $addFields = [
		'couponid','userid','amount','min','max','status','createtime','sort','featured'
    ];
    public static $updateFields = [
		'couponid','userid','amount','orderid','min','max','status','createtime','sort','featured'
    ];
    public static $detailFields = '*';
    public static $overviewFields = [
		'couponid','userid','amount','orderid','min','max','status','createtime','sort','featured'
    ];
    public static $countFilters = [
		'couponid','userid','amount','orderid','min','max','status','createtime','sort','featured'
    ];
    public static $depthStruct = [
        'amount'=>'double',
        'max'=>'double',
        'min'=>'double',
        'createtime'=>'int',
        'lasttime'=>'int',
        'sort'=>'int',
        'featured'=>'int',
    ];

}