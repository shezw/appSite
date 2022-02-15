<?php

namespace APS;

/**
 * 优惠券
 * Coupon
 * @package APS\custom\model
 */
class CommerceCoupon extends ASModel{

    const table             = 'commerce_coupon';
    const comment           = "电商-优惠券";
    const primaryid         = 'uid';
    const addFields         = ['uid','saasid','userid','amount','min','max','status','createtime'];
    const updateFields      = ['uid','saasid','userid','amount','orderid','min','max','status','createtime'];
    const detailFields      = ['uid','saasid','userid','amount','orderid','min','max','status','createtime'];
    const listFields        = ['uid','saasid','userid','amount','orderid','min','max','status','createtime'];
    const overviewFields    = ['uid','saasid','userid','amount','orderid','min','max','status','createtime'];
    const filterFields      = ['uid','saasid','userid','amount','orderid','min','max','status','createtime'];
    const depthStruct = [
        'amount'=>DBField_Decimal,
        'max'=>DBField_Decimal,
        'min'=>DBField_Decimal,
        'sort'=>DBField_Int,
        'featured'=>DBField_Boolean,
        'createtime'=>DBField_TimeStamp,
        'lasttime'=>DBField_TimeStamp,
    ];

    const tableStruct = [

        'uid'=>         ['type'=>DBField_String,    'len'=>8,       'nullable'=>0,  'cmt'=>'优惠券ID',   'idx'=>DBIndex_Unique],
        'saasid'=>      ['type'=>DBField_String,    'len'=>8,       'nullable'=>1,  'cmt'=>'所属saas',   'idx'=>DBIndex_Index ],
        'userid'=>      ['type'=>DBField_String,    'len'=>8,       'nullable'=>1,  'cmt'=>'限定用户ID',  'idx'=>DBIndex_Index ],
        'orderid'=>     ['type'=>DBField_String,    'len'=>32,      'nullable'=>1,  'cmt'=>'使用订单ID',  'idx'=>DBIndex_Index ],
        'amount'=>      ['type'=>DBField_Decimal,   'len'=>'12,2',  'nullable'=>0,  'cmt'=>'总价 精度0.01元',    'dft'=>0,     ],
        'min'=>         ['type'=>DBField_Decimal,   'len'=>'12,2',  'nullable'=>0,  'cmt'=>'最小额度 精度0.01元', 'dft'=>0,     ],
        'max'=>         ['type'=>DBField_Decimal,   'len'=>'12,2',  'nullable'=>0,  'cmt'=>'最大额度 精度0.01元', 'dft'=>0,     ],

        'status'=>      ['type'=>DBField_String,    'len'=>12,  'nullable'=>0,      'cmt'=>'状态',    'dft'=>'enabled',       ],

        'createtime'=>  ['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'创建时间',       'idx'=>DBIndex_Index, ],
        'lasttime'=>    ['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'上一次更新时间', ],
    ];
}