<?php

namespace APS;

/**
 * 标签
 * Tag
 * @package APS\service
 */
class Tag extends ASModel {

    const table     = "item_tag";
    const comment   = '通用 标签';
    const primaryid = "uid";
    const alias     = 'tag';

    const addFields = [
        'uid','saasid','authorid','type',
        'title','cover','description',
        'sort','featured','status',
        'createtime','lasttime',
    ];
    const updateFields = [
        'uid','saasid','authorid','type',
        'title','cover','description',
        'sort','featured','status',
        'createtime','lasttime',
    ];
    const detailFields = [
        'uid','saasid','authorid','type',
        'title','cover','description',
        'sort','featured','status',
        'createtime','lasttime',
    ];
    const overviewFields = [
        'uid','saasid','authorid','type',
        'title','cover','description',
        'sort','featured','status',
        'createtime','lasttime',
    ];
    const listFields = [
        'uid','saasid','authorid','type',
        'title','cover','description',
        'sort','featured','status',
        'createtime','lasttime',
    ];
    const filterFields = [
        'uid','saasid','authorid','type',
        'sort','featured','status',
        'createtime','lasttime',
    ];
    const depthStruct = [
        'sort'=>DBField_Int,
        'featured'=>DBField_Boolean,
        'createtime'=>DBField_TimeStamp,
        'lasttime'=>DBField_TimeStamp,
    ];

    const tableStruct = [

        'uid'=>         ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'标签ID' ,   'idx'=>DBIndex_Unique ],
        'saasid'=>      ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'所属saas',  'idx'=>DBIndex_Index,],
        'authorid'=>    ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'创建人ID' ,  'idx'=>DBIndex_Index ],

        'type'=>        ['type'=>DBField_String,    'len'=>16,  'nullable'=>1,  'cmt'=>'添加type时 即为特定类型下的标签' ],
        'title'=>       ['type'=>DBField_String,    'len'=>32,  'nullable'=>0,  'cmt'=>'标签名' ],
        'cover'=>       ['type'=>DBField_String,    'len'=>255, 'nullable'=>1,  'cmt'=>'封面' ],
        'description'=> ['type'=>DBField_String,    'len'=>255, 'nullable'=>1,  'cmt'=>'描述' ],

        'status'=>      ['type'=>DBField_String,    'len'=>12,  'nullable'=>0,  'cmt'=>'状态',    'dft'=>'enabled', ],

        'createtime'=>  ['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'创建时间',           'idx'=>DBIndex_Index, ],
        'lasttime'=>    ['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'上一次更新时间', ],
        'featured'=>    ['type'=>DBField_Boolean,  'len'=>1,  'nullable'=>0,  'cmt'=>'置顶',  'dft'=>0,    'idx'=>DBIndex_Index, ],
        'sort'=>        ['type'=>DBField_Int,      'len'=>5,  'nullable'=>0,  'cmt'=>'优先排序','dft'=>0,   'idx'=>DBIndex_Index, ],

    ];

}