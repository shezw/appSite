<?php

namespace APS;

/**
 * 横幅/轮播图
 * Banner
 * @package APS\service
 */
class Banner extends ASModel {

    const table     = "item_banner";
    const comment   = '横幅/轮播图';
    const primaryid = "uid";
    const addFields = [
        'uid','saasid','position',
        'title','cover','link',
        'clicktimes','viewtimes',
        'status','sort','featured',
        'createtime','lasttime',
    ];
    const updateFields = [
        'title','cover','link','position',
        'clicktimes','viewtimes',
        'status','sort','featured',
        'createtime','lasttime',
    ];
    const detailFields = [
        'uid','saasid','position',
        'title','cover','link',
        'clicktimes','viewtimes',
        'status','sort','featured',
        'createtime','lasttime',
    ];
    const overviewFields = [
        'uid','saasid','position',
        'title','cover','link',
        'clicktimes','viewtimes',
        'status','sort','featured',
        'createtime','lasttime',
    ];
    const listFields = [
        'uid','saasid','position',
        'title','cover','link',
        'clicktimes','viewtimes',
        'status','sort','featured',
        'createtime','lasttime',
    ];
    const filterFields = [
        'uid','saasid','position',
        'status','sort','featured',
        'createtime','lasttime',
    ];
    const depthStruct = [
        'clicktimes'=>DBField_Int,
        'viewtimes'=>DBField_Int,
        'sort'=>DBField_Int,
        'featured'=>DBField_Boolean,
        'createtime'=>DBField_TimeStamp,
        'lasttime'=>DBField_TimeStamp,
    ];

    const tableStruct = [

        'uid'=>         ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'轮播图ID' ,    'idx'=>DBIndex_Unique ],
        'saasid'=>      ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'所属saas',     'idx'=>DBIndex_Index,],
        'cover'=>       ['type'=>DBField_String,    'len'=>255, 'nullable'=>1,  'cmt'=>'封面 大图' ],
        'position'=>    ['type'=>DBField_String,    'len'=>64,  'nullable'=>1,  'cmt'=>'banner位置' ],
        'title'=>       ['type'=>DBField_String,    'len'=>64,  'nullable'=>0,  'cmt'=>'标题32字以内 分词' ,   'idx'=>DBIndex_FullText ],
        'link'=>        ['type'=>DBField_String,    'len'=>255, 'nullable'=>0,  'cmt'=>'链接' ],

        'clicktimes'=>  ['type'=>DBField_Int,       'len'=>16,  'nullable'=>0,  'cmt'=>'点击次数',  'dft'=>0, ],
        'viewtimes'=>   ['type'=>DBField_Int,       'len'=>13,  'nullable'=>0,  'cmt'=>'播放次数',  'dft'=>0, ],
        'status'=>      ['type'=>DBField_String,    'len'=>12,  'nullable'=>0,  'cmt'=>'状态',      'dft'=>'enabled', ],

        'createtime'=>  ['type'=>DBField_TimeStamp, 'len'=>13, 'nullable'=>0,  'cmt'=>'创建时间',                      'idx'=>DBIndex_Index, ],
        'lasttime'=>    ['type'=>DBField_TimeStamp, 'len'=>13, 'nullable'=>0,  'cmt'=>'上一次更新时间', ],
        'featured'=>    ['type'=>DBField_Boolean,   'len'=>1,  'nullable'=>0,  'cmt'=>'置顶',  'dft'=>0,               'idx'=>DBIndex_Index, ],
        'sort'=>        ['type'=>DBField_Int,       'len'=>5,  'nullable'=>0,  'cmt'=>'优先排序','dft'=>0,              'idx'=>DBIndex_Index, ],

    ];

}