<?php

namespace APS;

/**
 * 地区
 * Area
 * @package APS
 */
class Area extends ASModel{

    const table     = "item_area";
    const comment   = "地区";
    const primaryid = "uid";
    const addFields = [
        'uid','authorid','parentid',
        'title','cover','description','gallery',
        'mergename','shortname','mergeshortname',
        'code','zipcode',
        'location','lng','lat',
        'level',
        'sort','featured','status','createtime','lasttime'
    ];
    const updateFields = [
        'uid','authorid','parentid',
        'title','cover','description','gallery',
        'mergename','shortname','mergeshortname',
        'code','zipcode',
        'location','lng','lat',
        'level',
        'sort','featured','status','createtime','lasttime'
    ];
    const detailFields = [
        'uid','authorid','parentid',
        'title','cover','description','gallery',
        'mergename','shortname','mergeshortname',
        'code','zipcode',
        'lng','lat',
        'level',
        'sort','featured','status','createtime','lasttime'
    ];
    const overviewFields = [
        'uid','authorid','parentid',
        'title','cover','description','gallery',
        'mergename','shortname','mergeshortname',
        'code','zipcode',
        'lng','lat',
        'level',
        'sort','featured','status','createtime','lasttime'
    ];
    const listFields = [
        'uid','authorid','parentid',
        'title','cover','description','gallery',
        'mergename','shortname','mergeshortname',
        'code','zipcode',
        'lng','lat',
        'level',
        'sort','featured','status','createtime','lasttime'
    ];
    const filterFields = [
        'uid','authorid','parentid',
        'title',
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
        'lng'=>DBField_Decimal,
        'lat'=>DBField_Decimal,
    ];

    const tableStruct = [

        'uid'=>         ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'分类ID',  'idx'=>DBIndex_Unique ],
        'authorid'=>    ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'创建人ID', 'idx'=>DBIndex_Index ],
        'parentid'=>    ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'上一级ID', 'idx'=>DBIndex_Index ],

        'title'=>       ['type'=>DBField_String,    'len'=>32,  'nullable'=>0,  'cmt'=>'分类名'],
        'description'=> ['type'=>DBField_String,    'len'=>255, 'nullable'=>1,  'cmt'=>'描述'],
        'cover'=>       ['type'=>DBField_String,    'len'=>255, 'nullable'=>1,  'cmt'=>'封面'],
        'gallery'=>     ['type'=>DBField_RichText,  'len'=>-1,  'nullable'=>1,  'cmt'=>'相册'],

        'mergename'=>   ['type'=>DBField_String,    'len'=>64,  'nullable'=>1,  'cmt'=>'合并名称' ],
        'shortname'=>   ['type'=>DBField_String,    'len'=>32,  'nullable'=>1,  'cmt'=>'简称' ],
        'mergeshortname'=>['type'=>DBField_String,    'len'=>32,  'nullable'=>1,  'cmt'=>'合并简称' ],
        'level'=>       ['type'=>DBField_Int,       'len'=>3,   'nullable'=>1,  'cmt'=>'区域级别' ],
        'code'=>        ['type'=>DBField_String,    'len'=>12,  'nullable'=>1,  'cmt'=>'区号' ],
        'zipcode'=>     ['type'=>DBField_String,    'len'=>12,  'nullable'=>1,  'cmt'=>'邮编' ],
        'location'=>    ['type'=>DBField_Location,  'len'=>-1,  'nullable'=>0,  'cmt'=>'定位 GeomFromWKB'  ,      'idx'=>DBIndex_Spatial ],
        'lng'=>         ['type'=>DBField_Decimal,   'len'=>'14,10', 'nullable'=>0,  'cmt'=>'经度' ,   'dft'=>0,       ],
        'lat'=>         ['type'=>DBField_Decimal,   'len'=>'14,10', 'nullable'=>0,  'cmt'=>'纬度' ,   'dft'=>0,       ],
        // 'geo'=>      ['type'=>DBFieldType_STRING,'len'=>32,      'cmt'=>'形状 ASJson' ],

        'status'=>      ['type'=>DBField_String,    'len'=>12,  'nullable'=>0,  'cmt'=>'状态',    'dft'=>'enabled', ],

        'createtime'=>  ['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'创建时间',                      'idx'=>DBIndex_Index, ],
        'lasttime'=>    ['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'上一次更新时间', ],
        'featured'=>    ['type'=>DBField_Boolean,  'len'=>1,  'nullable'=>0,  'cmt'=>'置顶',  'dft'=>0,               'idx'=>DBIndex_Index, ],
        'sort'=>        ['type'=>DBField_Int,      'len'=>5,  'nullable'=>0,  'cmt'=>'优先排序','dft'=>0,              'idx'=>DBIndex_Index, ],

    ];

    public function listChild( DBConditions $conditions , string $uid , int $page = 1, int $size = 15, string $sort = null ): ASResult
    {
        $conditions->and(static::primaryid)->equal($uid);

        return $this->list($conditions,$page,$size,$sort);
    }

    // 查询对应的下级
    public function countChild( DBConditions $conditions , string $uid ): ASResult
    {
        $conditions->and(static::primaryid)->equal($uid);
        return $this->count($conditions );
    }

    public function getParents( string $uid, array $tmp = null ): array
    {
        $parents = $tmp ?? [];

        $area = $this->detail($uid)->getContent();

        if( isset($area['parentid']) ){

            $parents[] = $area['parentid'];
            $parents = $this->getParents( $area['parentid'], $parents );
        }
        return $parents;
    }


}