<?php

namespace APS;

/**
 * 平台化
 * Saas
 * @package APS
 */
class Saas extends ASModel{

    const table     = "item_saas";
    const comment   = "SAAS平台化";
    const primaryid = "uid";
    const addFields = [
        'uid','alias',
        'title','cover','description','gallery',
        'level',
        'sort','featured','status','createtime','lasttime'
    ];
    const updateFields = [
        'uid','alias',
        'title','cover','description','gallery',
        'level',
        'sort','featured','status','createtime','lasttime'
    ];
    const detailFields = [
        'uid','alias',
        'title','cover','description','gallery',
        'level',
        'sort','featured','status','createtime','lasttime'
    ];
    const overviewFields = [
        'uid','alias',
        'title','cover','description','gallery',
        'level',
        'sort','featured','status','createtime','lasttime'
    ];
    const listFields = [
        'uid','alias',
        'title','cover','description','gallery',
        'level',
        'sort','featured','status','createtime','lasttime'
    ];
    const filterFields = [
        'uid',
        'alias',
        'level',
        'location',
        'sort','featured','status','createtime','lasttime',
    ];
    const depthStruct = [
        'level'=>DBField_Int,
        'sort'=>DBField_Int,
        'featured'=>DBField_Int,
        'createtime'=>DBField_Int,
        'lasttime'=>DBField_Int,
        'gallery'=>DBField_Json,
    ];

    const tableStruct = [

        'uid'=>         ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'分类ID',  'idx'=>DBIndex_Unique ],
        'saasid'=>      ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'所属saas','idx'=>DBIndex_Index,],

        'alias'=>       ['type'=>DBField_String,    'len'=>32,  'nullable'=>0,  'cmt'=>'别称',  'idx'=>DBIndex_Unique ],
        'title'=>       ['type'=>DBField_String,    'len'=>32,  'nullable'=>0,  'cmt'=>'分类名'],
        'description'=> ['type'=>DBField_String,    'len'=>255, 'nullable'=>1,  'cmt'=>'描述'],
        'cover'=>       ['type'=>DBField_String,    'len'=>255, 'nullable'=>1,  'cmt'=>'封面'],
        'gallery'=>     ['type'=>DBField_RichText,  'len'=>-1,  'nullable'=>1,  'cmt'=>'相册'],

        'level'=>       ['type'=>DBField_Int,       'len'=>3,   'nullable'=>1,  'cmt'=>'区域级别' ],
        'status'=>      ['type'=>DBField_String,    'len'=>12,  'nullable'=>0,  'cmt'=>'状态',    'dft'=>'enabled', ],

        'createtime'=>  ['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'创建时间',                      'idx'=>DBIndex_Index, ],
        'lasttime'=>    ['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'上一次更新时间', ],
        'featured'=>    ['type'=>DBField_Boolean,  'len'=>1,  'nullable'=>0,  'cmt'=>'置顶',  'dft'=>0,               'idx'=>DBIndex_Index, ],
        'sort'=>        ['type'=>DBField_Int,      'len'=>5,  'nullable'=>0,  'cmt'=>'优先排序','dft'=>0,              'idx'=>DBIndex_Index, ],

    ];

}