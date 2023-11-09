<?php

namespace APS;

/**
 * 地铁
 * Subway
 * @package APS
 */
class Subway extends ASModel{

    const table     = "item_subway";
    const comment   = "地铁";
    const primaryid = "uid";
    const addFields  = [
        'uid','areaid','authorid','parentid',
        'title','description','cover','gallery',
        'location','lng','lat',
        'status','createtime','lasttime','featured','sort',
    ];
    const updateFields  = [
        'areaid','authorid','parentid',
        'title','description','cover','gallery',
        'location','lng','lat',
        'status','createtime','lasttime','featured','sort',
    ];
    const detailFields  = [
        'uid','areaid','authorid','parentid',
        'title','description','cover','gallery',
        'lng','lat',
        'status','createtime','lasttime','featured','sort',
    ];
    const overviewFields  = [
        'uid','areaid','authorid','parentid',
        'title','description','cover','gallery',
        'lng','lat',
        'status','createtime','lasttime','featured','sort',
    ];
    const listFields  = [
        'uid','areaid','authorid','parentid',
        'title','description','cover','gallery',
        'lng','lat',
        'status','createtime','lasttime','featured','sort',
    ];
    const filterFields  = [
        'uid','areaid','authorid','parentid',
        'title','description',
        'status','createtime','lasttime','featured','sort',
    ];
    const depthStruct  = [
        'gallery'=>DBField_Json,
        'lng'=>DBField_Decimal,
        'lat'=>DBField_Decimal,
        'sort'=>DBField_Int,
        'featured'=>DBField_Boolean,
        'createtime'=>DBField_TimeStamp,
        'lasttime'=>DBField_TimeStamp,
    ];

    const tableStruct = [

        'uid'=>         ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'地铁ID',  'idx'=>DBIndex_Unique ],
        'authorid'=>    ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'创建人ID', 'idx'=>DBIndex_Index ],
        'parentid'=>    ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'上一级ID', 'idx'=>DBIndex_Index ],
        'areaid'=>      ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'地区ID',  'idx'=>DBIndex_Index ],

        'title'=>       ['type'=>DBField_String,    'len'=>32,  'nullable'=>0,  'cmt'=>'分类名'],
        'description'=> ['type'=>DBField_String,    'len'=>255, 'nullable'=>1,  'cmt'=>'描述'],
        'cover'=>       ['type'=>DBField_String,    'len'=>255, 'nullable'=>1,  'cmt'=>'封面'],
        'gallery'=>     ['type'=>DBField_Json,      'len'=>-1,  'nullable'=>1,  'cmt'=>'相册'],

        'location'=>    ['type'=>DBField_Location,  'len'=>-1,  'nullable'=>0,  'cmt'=>'定位 GeomFromWKB'  ,      'idx'=>DBIndex_Spatial ],
        'lng'=>         ['type'=>DBField_Decimal,   'len'=>'14,10', 'nullable'=>0,  'cmt'=>'经度',    'dft'=>0, ],
        'lat'=>         ['type'=>DBField_Decimal,   'len'=>'14,10', 'nullable'=>0,  'cmt'=>'纬度',    'dft'=>0, ],

        'status'=>      ['type'=>DBField_String,    'len'=>12,  'nullable'=>0,  'cmt'=>'状态',    'dft'=>'enabled', ],

        'createtime'=>  ['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'创建时间',                   'idx'=>DBIndex_Index, ],
        'lasttime'=>    ['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'上一次更新时间', ],
        'featured'=>    ['type'=>DBField_Boolean,  'len'=>1,  'nullable'=>0,  'cmt'=>'置顶',  'dft'=>0,            'idx'=>DBIndex_Index, ],
        'sort'=>        ['type'=>DBField_Int,      'len'=>5,  'nullable'=>0,  'cmt'=>'优先排序','dft'=>0,           'idx'=>DBIndex_Index, ],

    ];
}