<?php

namespace APS;

/**
 * 电商 - 库存模块
 * CommerceStock
 * @package APS\service
 */
class CommerceStock extends ASModel{

    const table     = "commerce_stock";
    const comment   = '电商-库存';
    const primaryid = "uid";

    const addFields = [
        'uid','productid','authorid',
        'type','mode','title','cover','description','stock',
        'sort','featured','status',
        'createtime','lasttime',
    ];

    const updateFields = [
        'type','mode','title','cover','description','stock',
        'sort','featured','status',
        'createtime','lasttime',
    ];
    const detailFields  = [
        'uid','productid','authorid',
        'type','mode','title','cover','description','stock',
        'sort','featured','status',
        'createtime','lasttime',
    ];
    const overviewFields  = [
        'uid','productid','authorid',
        'type','mode','title','cover','description','stock',
        'sort','featured','status',
        'createtime','lasttime',
    ];
    const listFields  = [
        'uid','productid','authorid',
        'type','mode','title','cover','description','stock',
        'sort','featured','status',
        'createtime','lasttime',
    ];
    const filterFields = [
        'uid','productid','authorid',
        'type','mode','title','cover','description','stock',
        'sort','featured','status',
        'createtime','lasttime',
    ];
    const depthStruct  = [
        'stock'=>DBField_Int,
        'featured'=>DBField_Boolean,
        'createtime'=>DBField_TimeStamp,
        'lasttime'=>DBField_TimeStamp,
        'sort'=>DBField_Int
    ];

    const tableStruct = [

        'uid'=>         ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,      'cmt'=>'优惠券ID', 'idx'=>DBIndex_Unique ],
        'productid'=>   ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,      'cmt'=>'所属产品ID','idx'=>DBIndex_Index ],
        'authorid'=>    ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,      'cmt'=>'订单ID',  'idx'=>DBIndex_Index ],
        'type'=>        ['type'=>DBField_String,    'len'=>16,  'nullable'=>0,      'cmt'=>'类型',    'dft'=>0,       ],
        'mode'=>        ['type'=>DBField_String,    'len'=>16,  'nullable'=>0,      'cmt'=>'模式',    'dft'=>0,       ],

        'title'=>       ['type'=>DBField_String,    'len'=>127, 'nullable'=>0,      'cmt'=>'标题 60字以内 分词' ,  'idx'=>DBIndex_FullText ],
        'cover'=>       ['type'=>DBField_String,    'len'=>255, 'nullable'=>1,      'cmt'=>'缩略图 小图' ],
        'description'=> ['type'=>DBField_String,    'len'=>255, 'nullable'=>1,      'cmt'=>'描述' ],
        'stock'=>       ['type'=>DBField_Int,       'len'=>8,   'nullable'=>0,      'cmt'=>'剩余库存',  'dft'=>1, ],

        'status'=>      ['type'=>DBField_String,    'len'=>12,  'nullable'=>0,      'cmt'=>'状态',    'dft'=>'enabled',         ],

        'createtime'=>  ['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'创建时间',           'idx'=>DBIndex_Index, ],
        'lasttime'=>    ['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'上一次更新时间', ],
    ];
}