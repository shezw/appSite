<?php

namespace APS;

/**
 * 商圈(地区)
 * District
 * @package APS
 */
class District extends ASModel{

    const table     = "item_district";
    const comment   = "商圈(地区)";
    const primaryid = "uid";
    const addFields  = [
        'uid','saasid','regionid','areaid','authorid','parentid',
        'title','description','cover','gallery',
        'location','lng','lat','geo',
        'status','createtime','lasttime','featured','sort',
    ];
    const updateFields  = [
        'regionid','areaid','authorid','parentid',
        'title','description','cover','gallery',
        'location','lng','lat',
        'status','createtime','lasttime','featured','sort',
    ];
    const detailFields  = [
        'uid','saasid','regionid','areaid','authorid','parentid',
        'title','description','cover','gallery',
        'lng','lat','geo',
        'status','createtime','lasttime','featured','sort',
    ];
    const overviewFields  = [
        'uid','saasid','regionid','areaid','authorid','parentid',
        'title','description','cover','gallery',
        'lng','lat',
        'status','createtime','lasttime','featured','sort',

    ];
    const listFields  = [
        'uid','saasid','regionid','areaid','authorid','parentid',
        'title','description','cover','gallery',
        'lng','lat',
        'status','createtime','lasttime','featured','sort',
    ];
    const filterFields  = [
        'uid','saasid','regionid','areaid','authorid','parentid',
        'title','description',
        'status','createtime','lasttime','featured','sort',
    ];
    const depthStruct  = [
        'lng'=>DBField_Decimal,
        'lat'=>DBField_Decimal,
        'sort'=>DBField_Int,
        'featured'=>DBField_Boolean,
        'createtime'=>DBField_TimeStamp,
        'lasttime'=>DBField_TimeStamp,
    ];

    const tableStruct = [

        'uid'=>         ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'商圈ID',  'idx'=>DBIndex_Unique ],
        'saasid'=>      ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'所属saas','idx'=>DBIndex_Index,],
        'regionid'=>    ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'地域ID',  'idx'=>DBIndex_Index ],
        'areaid'=>      ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'地区ID',  'idx'=>DBIndex_Index ],
        'authorid'=>    ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'创建人ID', 'idx'=>DBIndex_Index ],
        'parentid'=>    ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'上一级ID', 'idx'=>DBIndex_Index ],

        'title'=>       ['type'=>DBField_String,    'len'=>32,  'nullable'=>0,  'cmt'=>'分类名'],
        'description'=> ['type'=>DBField_String,    'len'=>255, 'nullable'=>1,  'cmt'=>'描述'],
        'cover'=>       ['type'=>DBField_String,    'len'=>255, 'nullable'=>1,  'cmt'=>'封面'],
        'gallery'=>     ['type'=>DBField_Json,      'len'=>-1,  'nullable'=>1,  'cmt'=>'相册'],

        'location'=>    ['type'=>DBField_Location,  'len'=>-1,  'nullable'=>0,  'cmt'=>'定位 GeomFromWKB'  ,      'idx'=>DBIndex_Spatial ],
        'lng'=>         ['type'=>DBField_Decimal,   'len'=>'14,10', 'nullable'=>0,  'cmt'=>'经度' ,   'dft'=>0,    ],
        'lat'=>         ['type'=>DBField_Decimal,   'len'=>'14,10', 'nullable'=>0,  'cmt'=>'纬度' ,   'dft'=>0,    ],
        'geo'=>         ['type'=>DBField_Json,       'len'=>-1,  'nullable'=>1,     'cmt'=>'形状 ASJson' ],

        'status'=>      ['type'=>DBField_String,    'len'=>12,  'nullable'=>0,  'cmt'=>'状态',    'dft'=>'enabled', ],

        'createtime'=>  ['type'=>DBField_TimeStamp, 'len'=>13, 'nullable'=>0,  'cmt'=>'创建时间',                      'idx'=>DBIndex_Index, ],
        'lasttime'=>    ['type'=>DBField_TimeStamp, 'len'=>13, 'nullable'=>0,  'cmt'=>'上一次更新时间', ],
        'featured'=>    ['type'=>DBField_Boolean,   'len'=>1,  'nullable'=>0,  'cmt'=>'置顶',  'dft'=>0,               'idx'=>DBIndex_Index, ],
        'sort'=>        ['type'=>DBField_Int,       'len'=>5,  'nullable'=>0,  'cmt'=>'优先排序','dft'=>0,              'idx'=>DBIndex_Index, ],

    ];
}