<?php

namespace APS;

/**
 * 行业
 * Industry
 * @package APS
 */
class Industry extends ASModel{

    const table     = "item_industry";
    const comment   = "行业";
    const primaryid = "uid";
    const addFields  = [
        'uid','saasid','parentid',
        'title','cover','description','level',
        'sort','featured','status',
    ];
    const updateFields  = [
        'uid','saasid','parentid',
        'title','cover','description','level',
        'sort','featured','status',
    ];
    const detailFields  = [
        'uid','saasid','parentid',
        'title','cover','description','level',
        'sort','featured','status',
        'createtime','lasttime',
    ];
    const overviewFields  = [
        'uid','saasid','parentid',
        'title','cover','description','level',
        'sort','featured','status',
        'createtime','lasttime',
    ];
    const listFields  = [
        'uid','saasid','parentid',
        'title','cover','description','level',
        'sort','featured','status',
        'createtime','lasttime',
    ];
    const filterFields  = [
        'uid','saasid','parentid',
        'sort','featured','status',
        'createtime','lasttime',
    ];
    const depthStruct  = [
        'level'=>DBField_Int,
        'sort'=>DBField_Int,
        'featured'=>DBField_Boolean,
        'createtime'=>DBField_TimeStamp,
        'lasttime'=>DBField_TimeStamp,
    ];

    const tableStruct = [

        'uid'=>         ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'行业ID' , 'idx'=>DBIndex_Unique ],
        'saasid'=>      ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'所属saas','idx'=>DBIndex_Index,],
        'parentid'=>    ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'上一级ID', 'idx'=>DBIndex_Index ],
        'level'=>       ['type'=>DBField_Int,       'len'=>3,   'nullable'=>1,  'cmt'=>'级别' ],

        //基础字段
        'title'=>       ['type'=>DBField_String,    'len'=>32,  'nullable'=>0,  'cmt'=>'名称名' ,  'idx'=>DBIndex_FullText ],
        'description'=> ['type'=>DBField_String,    'len'=>255, 'nullable'=>1,  'cmt'=>'描述' ,   'idx'=>DBIndex_FullText ],
        'cover'=>       ['type'=>DBField_String,    'len'=>255, 'nullable'=>1,  'cmt'=>'封面' ],

        'status'=>      ['type'=>DBField_String,    'len'=>12,  'nullable'=>0,  'cmt'=>'状态',    'dft'=>'enabled', ],

        'createtime'=>  ['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'创建时间',          'idx'=>DBIndex_Index, ],
        'lasttime'=>    ['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'上一次更新时间', ],
        'featured'=>    ['type'=>DBField_Boolean,  'len'=>1,  'nullable'=>0,  'cmt'=>'置顶',  'dft'=>0,   'idx'=>DBIndex_Index, ],
        'sort'=>        ['type'=>DBField_Int,      'len'=>5,  'nullable'=>0,  'cmt'=>'优先排序','dft'=>0,  'idx'=>DBIndex_Index, ],
    ];
}