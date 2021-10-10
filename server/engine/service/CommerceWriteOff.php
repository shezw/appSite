<?php

namespace APS;

/**
 * 电商 - 核销
 * CommerceWriteOff
 * @package APS
 */
class CommerceWriteOff extends ASModel
{

    const table     = "commerce_writeoff";
    const comment   = '电商-核销';
    const primaryid = "uid";
    const addFields  = [
        'uid','orderid','targetid','itemid','userid',
        'status','createtime','lasttime',
    ];
    const updateFields  = [
        'status',
    ];
    const detailFields  = [
        'uid','orderid','targetid','itemid','userid',
        'status','createtime','lasttime',
    ];
    const overviewFields  = [
        'uid','orderid','targetid','itemid','userid',
        'status','createtime','lasttime',
    ];
    const listFields  = [
        'uid','orderid','targetid','itemid','userid',
        'status','createtime','lasttime',
    ];
    const filterFields = [
        'uid','orderid','targetid','itemid','userid',
        'status','createtime','lasttime',
    ];
    const depthStruct  = [
        'createtime'=>DBField_TimeStamp,
        'lasttime'=>DBField_TimeStamp,
    ];

    const tableStruct = [

        'uid'=>         ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'核销ID',  'idx'=>DBIndex_Unique ],
        'orderid'=>     ['type'=>DBField_String,    'len'=>32,  'nullable'=>0,  'cmt'=>'订单ID',  'idx'=>DBIndex_Index ],
        'itemid'=>      ['type'=>DBField_String,    'len'=>32,  'nullable'=>1,  'cmt'=>'对象ID',  'idx'=>DBIndex_Index ],
        'targetid'=>    ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'核销绑定ID','idx'=>DBIndex_Index ],
        'userid'=>      ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'用户ID',  'idx'=>DBIndex_Index ],

        'status'=>      ['type'=>DBField_String,    'len'=>12,  'nullable'=>0,  'cmt'=>'状态',    'dft'=>'enabled',         ],

        'createtime'=>  ['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'创建时间',           'idx'=>DBIndex_Index, ],
        'lasttime'=>    ['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'上一次更新时间', ],
    ];

}