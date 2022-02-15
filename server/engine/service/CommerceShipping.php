<?php

namespace APS;

/**
 * 物流
 * CommerceShipping
 * @package APS\custom\model
 */
class CommerceShipping extends ASModel{

    const table = 'commerce_shipping';
    const comment = '电商-物流';
    const primaryid = 'uid';
    const alias     = 'shipping';

    const addFields = [
		'uid','saasid','title','cover','description','amount','details','status','createtime','sort','featured'
    ];
    const updateFields = [
		'uid','saasid','title','cover','description','amount','details','status','createtime','sort','featured'
    ];
    const detailFields = [
        'uid','saasid','title','cover','description','amount','details','status','createtime','sort','featured'
    ];
    const overviewFields = [
		'uid','saasid','title','cover','description','amount','details','status','createtime','sort','featured'
    ];
    const listFields = [
        'uid','saasid','title','cover','description','amount','status','createtime','sort','featured'
    ];
    const filterFields = [
		'uid','saasid','amount','status','createtime','sort','featured'
    ];
    const depthStruct = [
        'amount'=>DBField_Decimal,
        'details'=>DBField_Json,
        'createtime'=>DBField_TimeStamp,
        'lasttime'=>DBField_TimeStamp,
        'sort'=>DBField_Int,
        'featured'=>DBField_Boolean,
    ];

    const tableStruct = [

        'uid'=>         ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,      'cmt'=>'索引ID',  'idx'=>DBIndex_Unique ],
        'saasid'=>      ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,      'cmt'=>'所属saas',  'idx'=>DBIndex_Index,],
        'title'=>       ['type'=>DBField_String,    'len'=>127, 'nullable'=>0,      'cmt'=>'标题 60字以内 分词' ,  'idx'=>DBIndex_FullText ],
        'cover'=>       ['type'=>DBField_String,    'len'=>255, 'nullable'=>1,      'cmt'=>'缩略图 小图' ],
        'description'=> ['type'=>DBField_String,    'len'=>255, 'nullable'=>1,      'cmt'=>'描述' ],
        'amount'=>      ['type'=>DBField_Decimal,   'len'=>'12,2',  'nullable'=>0,  'cmt'=>'总价 精度0.01元' ,   'dft'=>0,       ],
        'details'=>     ['type'=>DBField_Json,      'len'=>-1,  'nullable'=>1,      'cmt'=>'详情记录 ASJson',  ],

        'status'=>      ['type'=>DBField_String,    'len'=>12,  'nullable'=>0,      'cmt'=>'状态',    'dft'=>'enabled',         ],

        'createtime'=>  ['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'创建时间',           'idx'=>DBIndex_Index, ],
        'lasttime'=>    ['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'上一次更新时间', ],
        'featured'=>    ['type'=>DBField_Boolean,  'len'=>1,  'nullable'=>0,  'cmt'=>'置顶',  'dft'=>0,    'idx'=>DBIndex_Index, ],
        'sort'=>        ['type'=>DBField_Int,      'len'=>5,  'nullable'=>0,  'cmt'=>'优先排序','dft'=>0,   'idx'=>DBIndex_Index, ],
    ];
}