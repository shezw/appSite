<?php

namespace APS;

/**
 * 页面
 * Page
 * @package APS\service
 */
class Page extends ASModel
{

    const table     = "item_page";
    const comment   = '页面';
    const primaryid = "uid";
    const alias     = 'page';

    const addFields = [
        'uid', 'alias','saasid','authorid',
        'title','cover', 'introduce',
        'status', 'viewtimes', 'featured', 'sort'
    ];
    const updateFields = [
        'alias',
        'title','cover', 'introduce',
        'status', 'viewtimes', 'featured', 'sort'
    ];
    const detailFields = [
        'uid', 'alias','saasid','authorid',
        'title','cover', 'introduce', 'status', 'viewtimes',
        'createtime', 'lasttime', 'featured', 'sort'
    ];
    const publicDetailFields = [
        'uid', 'alias','saasid','authorid',
        'title','cover', 'introduce', 'status', 'viewtimes',
        'createtime', 'lasttime', 'featured', 'sort'
    ];
    const overviewFields = [
        'uid', 'alias','saasid','authorid',
        'title','cover', 'introduce', 'status', 'viewtimes',
        'createtime', 'lasttime', 'featured', 'sort'
    ];
    const listFields = [
        'uid', 'alias','saasid','authorid',
        'title','cover', 'status', 'viewtimes',
        'createtime', 'lasttime', 'featured', 'sort'
    ];
    const publicListFields = [
        'uid', 'alias','saasid','authorid',
        'title','cover', 'status', 'viewtimes',
        'createtime', 'lasttime', 'featured', 'sort'

    ];
    const filterFields = [
        'uid','alias', 'saasid','authorid',
        'title','status', 'viewtimes',
        'createtime', 'lasttime', 'featured', 'sort'
    ];
    const depthStruct = [
        'viewtimes'=>DBField_Int,
        'createtime'=>DBField_TimeStamp,
        'lasttime'=>DBField_TimeStamp,
        'sort'=>DBField_Int,
        'featured'=>DBField_Boolean
    ];

    const tableStruct = [

        'uid'=>         ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'通知ID' , 'idx'=>DBIndex_Unique ],
        'alias'=>       ['type'=>DBField_String,    'len'=>16,  'nullable'=>0,  'cmt'=>'别称' ,        'idx'=>DBIndex_Unique ],
        'saasid'=>      ['type'=>DBField_String,  'len'=>8,   'nullable'=>1,     'cmt'=>'所属saas',  'idx'=>DBIndex_Index,],
        'authorid'=>    ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'创建人ID' ,        'idx'=>DBIndex_Index ],

        'title'=>       ['type'=>DBField_String,    'len'=>32,  'nullable'=>0,  'cmt'=>'分类名' ],
        'cover'=>       ['type'=>DBField_String,    'len'=>255, 'nullable'=>1,  'cmt'=>'封面 大图' ],
        'introduce'=>   ['type'=>DBField_RichText,  'len'=>-1,  'nullable'=>1,  'cmt'=>'详情 不限字数 分词' ,   'idx'=>DBIndex_FullText ],

        'status'=>      ['type'=>DBField_String,    'len'=>12,  'nullable'=>0,  'cmt'=>'状态',    'dft'=>'enabled', ],

        'viewtimes'=>   ['type'=>DBField_Int,       'len'=>16,  'nullable'=>0,  'cmt'=>'点击次数' , 'dft'=>0,       ],

        'createtime'=>  ['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'创建时间',             'idx'=>DBIndex_Index, ],
        'lasttime'=>    ['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'上一次更新时间', ],
        'featured'=>    ['type'=>DBField_Boolean,  'len'=>1,  'nullable'=>0,  'cmt'=>'置顶',  'dft'=>0,      'idx'=>DBIndex_Index, ],
        'sort'=>        ['type'=>DBField_Int,      'len'=>5,  'nullable'=>0,  'cmt'=>'优先排序','dft'=>0,     'idx'=>DBIndex_Index, ],
    ];
}