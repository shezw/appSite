<?php

namespace APS;

/**
 * 表单 - 合约
 * FormContract
 *
 * 合约可以提供线上合约的机制，其中双方的Sign由IBChian模块支持，即Sign使用Block的HASH，双方签署的信息可追述
 *
 * @package APS\service
 */
class FormContract extends ASModel{


    const table     = "form_contract";
    const comment   = '表单-合约';
    const primaryid = "uid";
    const addFields  = [
        'userid','saasid','targetid','itemid','itemtype',
        'title','description','cover','terms','information','attachments',
        'signa','signb',
        'price','paytype','time','payduration','custompay','payoffset','payments',
        'starttime','endtime',
        'status', 'createtime','lasttime'
    ];
    const updateFields  = [
        'information','attachments',
        'signa','signb',
        'status',
    ];
    const detailFields  = [
        'uid','saasid','userid','targetid','itemid','itemtype',
        'title','description','cover','terms','information','attachments',
        'signa','signb',
        'price','paytype','time','payduration','custompay','payoffset','payments',
        'starttime','endtime',
        'status', 'createtime','lasttime'
    ];
    const overviewFields  = [
        'uid','saasid','userid','targetid','itemid','itemtype',
        'title','description','cover',
        'signa','signb',
        'price','paytype','time','payduration','payoffset','payments',
        'starttime','endtime',
        'status', 'createtime','lasttime'
    ];
    const listFields  = [
        'uid','saasid','userid','targetid','itemid','itemtype',
        'title','description','cover','information',
        'signa','signb',
        'price','paytype','time','payduration','custompay','payoffset',
        'starttime','endtime',
        'status', 'createtime','lasttime'
    ];
    const filterFields = [
        'uid','saasid','userid','targetid','itemid','itemtype',
        'price','paytype','time','payduration',
        'starttime','endtime',
        'status', 'createtime','lasttime'
    ];
    const depthStruct  = [

        'price'=>DBField_Decimal,
        'payoffset'=>DBField_Int,

        'createtime'=>DBField_TimeStamp,
        'lasttime'=>DBField_TimeStamp,
        'starttime'=>DBField_TimeStamp,
        'endtime'=>DBField_TimeStamp,
        'attachments'=>DBField_Json,
        'information'=>DBField_Json
    ];


    const tableStruct = [

        'uid'=>     ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'索引ID' , 'idx'=>DBIndex_Unique ],
        'saasid'  =>['type'=>DBField_String,    'len'=>8,   'nullable'=>1, 'cmt'=>'所属saas',  'idx'=>DBIndex_Index,],
        'userid'=>  ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'用户ID' , 'idx'=>DBIndex_Index ],
        'targetid'=>['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'合约方ID' ,        'idx'=>DBIndex_Index ],
        'itemid'=>  ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'目标ID' , 'idx'=>DBIndex_Index ],
        'itemtype'=>['type'=>DBField_String,    'len'=>12,  'nullable'=>1,  'cmt'=>'目标类别' , 'idx'=>DBIndex_Index ],

        'title'=>   ['type'=>DBField_String,    'len'=>64,  'nullable'=>0,  'cmt'=>'名称' ],
        'description'=>   ['type'=>DBField_String,    'len'=>256, 'nullable'=>1,  'cmt'=>'描述' ],
        'cover'=>   ['type'=>DBField_String,    'len'=>256, 'nullable'=>1,  'cmt'=>'封面' ],
        'terms'=>   ['type'=>DBField_RichText,  'len'=>-1,  'nullable'=>1,  'cmt'=>'条款'],
        'information'=>   ['type'=>DBField_Json,  'len'=>-1,  'nullable'=>1,  'cmt'=>'备注信息'],
        'attachments'=>   ['type'=>DBField_Json,  'len'=>-1,  'nullable'=>1,  'cmt'=>'附件(云存储链接列表)'],

        'signa'=>   ['type'=>DBField_String,    'len'=>64,  'nullable'=>1,  'cmt'=>'签章A' ],
        'signb'=>   ['type'=>DBField_String,    'len'=>64,  'nullable'=>1,  'cmt'=>'签章B' ],

        'price'=>   ['type'=>DBField_Decimal,   'len'=>'12,2',  'nullable'=>0,  'cmt'=>'价格' ,   'dft'=>0,       ],
        'paytype'=> ['type'=>DBField_String,    'len'=>12,  'nullable'=>1,  'cmt'=>'支付类型( once一次性,loop循环,custom自定义 )' ],
        'payduration'=>   ['type'=>DBField_String,    'len'=>12,  'nullable'=>1,  'cmt'=>'支付周期( day天,month月,quarter季度,year年 )' ],
        'custompay'=>     ['type'=>DBField_String,    'len'=>256, 'nullable'=>1,  'cmt'=>'自定义支付方式' ],
        'payoffset'=>     ['type'=>DBField_Int,       'len'=> 3,  'nullable'=>0,  'cmt'=>'支付偏移量(偏移量 按天左右偏移)' ,    'dft'=>0,       ],
        'payments'=>['type'=>DBField_RichText,  'len'=>-1,  'nullable'=>1,  'cmt'=>'支付信息'],

        'starttime'=>     ['type'=>DBField_Int,       'len'=>11,  'nullable'=>0,  'cmt'=>'开始时间'  ,        'dft'=>0,       ],
        'endtime'=> ['type'=>DBField_Int,       'len'=>11,  'nullable'=>0,  'cmt'=>'结束时间'  ,        'dft'=>0,       ],

        'status'=>  ['type'=>DBField_String,    'len'=>12,  'nullable'=>0,  'cmt'=>'状态',    'dft'=>'pending',       ],

        'createtime'   =>['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'创建时间',                      'idx'=>DBIndex_Index, ],
        'lasttime'     =>['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'上一次更新时间', ],
    ];
}