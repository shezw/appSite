<?php

namespace APS;

/**
 * 电商 - 商品模块
 * CommerceProduct
 * @package APS\service
 */
class CommerceProduct extends ASModel{

    const table     = "commerce_product";
    const comment   = '电商-产品';
    const primaryid = "uid";

    const addFields = [
        'uid','saasid','categoryid','authorid',
        'type','mode','title','cover','gallery','video','link','tags','description','introduce','details',
        'price','sale','isvirtual','features','stock',
        'viewtimes','sort','featured','status',
        'createtime','lasttime',
    ];

    const updateFields = [
        'categoryid',
        'type','mode','title','cover','gallery','video','link','tags','description','introduce','details',
        'price','sale','isvirtual','features','stock',
        'viewtimes','sort','featured','status','createtime','lasttime'
    ];
    const detailFields  = [
        'uid','saasid','categoryid','authorid',
        'type','mode','title','cover','gallery','video','link','tags','description','introduce','details',
        'price','sale','isvirtual','features','stock',
        'viewtimes','sort','featured','status',
        'createtime','lasttime',
    ];
    const overviewFields  = [
        'uid','saasid','categoryid','authorid',
        'type','mode','title','cover','gallery','video','link','tags','description','introduce','details',
        'price','sale','isvirtual','features','stock',
        'viewtimes','sort','featured','status','createtime','lasttime'
    ];
    const listFields  = [
        'uid','saasid','categoryid','authorid',
        'type','mode','title','cover','description','link','tags',
        'price','sale','isvirtual','stock',
        'viewtimes','sort','featured','status','createtime','lasttime'
    ];
    const filterFields  = [
        'uid','saasid','categoryid','authorid',
        'type','mode','title','keyword',
        'price','sale','isvirtual','stock',
        'viewtimes','sort','featured','status','createtime','lasttime'
    ];
    const depthStruct  = [
        'details'=>DBField_Json,
        'features'=>DBField_Json,
        'isvirtual'=>DBField_Boolean,
        'price'=>DBField_Decimal,
        'sale'=>DBField_Decimal,
        'stock'=>DBField_Int,
        'gallery'=>DBField_Json,
        'tags'=>DBField_Json,
        'viewtimes'=>DBField_Int,
        'createtime'=>DBField_TimeStamp,
        'lasttime'=>DBField_TimeStamp,
        'featured'=>DBField_Boolean,
        'sort'=>DBField_Int
    ];

    const tableStruct = [

        'uid'=>         ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,      'cmt'=>'优惠券ID',  'idx'=>DBIndex_Unique ],
        'saasid'=>      ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,      'cmt'=>'所属saas',  'idx'=>DBIndex_Index,],
        'categoryid'=>  ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,      'cmt'=>'分类id' ],
        'authorid'=>    ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,      'cmt'=>'订单ID',  'idx'=>DBIndex_Index ],
        'type'=>        ['type'=>DBField_String,    'len'=>16,  'nullable'=>0,      'cmt'=>'类型',    'dft'=>0,       ],
        'mode'=>        ['type'=>DBField_String,    'len'=>16,  'nullable'=>0,      'cmt'=>'模式',    'dft'=>0,       ],

        'title'=>       ['type'=>DBField_String,    'len'=>127, 'nullable'=>0,      'cmt'=>'标题 60字以内 分词',  'idx'=>DBIndex_FullText ],
        'cover'=>       ['type'=>DBField_String,    'len'=>255, 'nullable'=>1,      'cmt'=>'缩略图 小图' ],
        'description'=> ['type'=>DBField_String,    'len'=>255, 'nullable'=>1,      'cmt'=>'描述' ],
        'introduce'=>   ['type'=>DBField_RichText,  'len'=>-1,  'nullable'=>1,      'cmt'=>'详情'],

        'gallery'=>     ['type'=>DBField_Json,      'len'=>-1,  'nullable'=>1,      'cmt'=>'图册' ],
        'attachments'=> ['type'=>DBField_Json,      'len'=>-1,  'nullable'=>1,      'cmt'=>'附件' ],
        'video'=>       ['type'=>DBField_String,    'len'=>255, 'nullable'=>1,      'cmt'=>'视频' ],
        'link'=>        ['type'=>DBField_String,    'len'=>255, 'nullable'=>1,      'cmt'=>'链接' ],
        'tags'=>        ['type'=>DBField_Json,    'len'=>255, 'nullable'=>1,      'cmt'=>'标签 最高255' ],

        'details'=>     ['type'=>DBField_Json,      'len'=>-1,  'nullable'=>1,      'cmt'=>'补充信息，如兑换码等' ],

        'price'=>       ['type'=>DBField_Decimal,   'len'=>'12,2',  'nullable'=>0,      'cmt'=>'价格',    'dft'=>0,       ],
        'sale'=>        ['type'=>DBField_Decimal,   'len'=>'12,2',  'nullable'=>0,      'cmt'=>'售价',    'dft'=>0,       ],
        'isvirtual'=>   ['type'=>DBField_Boolean,   'len'=>1,   'nullable'=>0,      'cmt'=>'是否虚拟', 'dft'=>0,       ],
        'features'=>    ['type'=>DBField_Json,      'len'=>-1,  'nullable'=>1,      'cmt'=>'补充信息，如兑换码等' ],
        'stock'=>       ['type'=>DBField_Int,       'len'=>8,   'nullable'=>0,      'dft'=>1,   'cmt'=>'剩余库存'],

        'viewtimes'=>   ['type'=>DBField_Int,       'len'=>13,  'nullable'=>0,      'cmt'=>'展示次数'  ,        'dft'=>0,       ],
        'status'=>      ['type'=>DBField_String,    'len'=>12,  'nullable'=>0,      'cmt'=>'状态',    'dft'=>'enabled',         ],

        'createtime'=>  ['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'创建时间',           'idx'=>DBIndex_Index, ],
        'lasttime'=>    ['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'上一次更新时间', ],
        'featured'=>    ['type'=>DBField_Boolean,  'len'=>1,  'nullable'=>0,  'cmt'=>'置顶',  'dft'=>0,    'idx'=>DBIndex_Index, ],
        'sort'=>        ['type'=>DBField_Int,      'len'=>5,  'nullable'=>0,  'cmt'=>'优先排序','dft'=>0,   'idx'=>DBIndex_Index, ],
    ];
}