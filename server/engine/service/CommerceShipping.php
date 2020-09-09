<?php

namespace APS;

/**
 * 一个模型的示范类
 * A Model Class sample
 * @package APS\custom\model
 */
class CommerceShipping extends ASModel{

    public static $table = 'commerce_shipping';
    public static $primaryid = 'shippingid';
    public static $addFields = [
		'shippingid','title','cover','description','amount','details','status','createtime','sort','featured'
    ];
    public static $updateFields = [
		'shippingid','title','cover','description','amount','details','status','createtime','sort','featured'
    ];
    public static $detailFields = '*';
    public static $overviewFields = [
		'shippingid','title','cover','description','amount','details','status','createtime','sort','featured'
    ];
    public static $countFilters = [
		'shippingid','amount','status','createtime','sort','featured'
    ];
    public static $depthStruct = [
        'amount'=>'double',
        'createtime'=>'int',
        'lasttime'=>'int',
        'sort'=>'int',
        'featured'=>'int',
    ];

}