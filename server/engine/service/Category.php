<?php

namespace APS;

/**
 * 通用分类
 * Category
 * @package APS\service
 */
class Category extends ASModel {

    const table     = "item_category";
    const comment   = "通用分类";
    const primaryid = "uid";
    const addFields = [
        'uid','alias','saasid','authorid','parentid','type',
        'title','cover','description',
        'sort','featured','status',
        'createtime','lasttime',
    ];
    const updateFields = [
        'authorid','alias','parentid','type',
        'title','cover','description',
        'sort','featured','status',
        'createtime','lasttime',
    ];
    const detailFields = [
        'uid','alias','saasid','authorid','parentid','type',
        'title','cover','description',
        'sort','featured','status',
        'createtime','lasttime',
    ];
    const overviewFields = [
        'uid','alias','saasid','authorid','parentid','type',
        'title','cover','description',
        'sort','featured','status',
        'createtime','lasttime',
    ];
    const listFields = [
        'uid','alias','saasid','authorid','parentid','type',
        'title','cover','description',
        'sort','featured','status',
        'createtime','lasttime',
    ];
    const filterFields = [
        'uid','alias','saasid','authorid','parentid','type',
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

        'uid'=>         ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'分类ID' , 'idx'=>DBIndex_Unique ],
        'saasid'=>      ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'所属saas',  'idx'=>DBIndex_Index,],
        'alias'=>       ['type'=>DBField_String,    'len'=>24,  'nullable'=>1,  'cmt'=>'别称' ,        'idx'=>DBIndex_Unique ],

        'title'=>       ['type'=>DBField_String,    'len'=>64,  'nullable'=>0,  'cmt'=>'分类名' ],
        'authorid'=>    ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'创建人ID' ,        'idx'=>DBIndex_Index ],
        'parentid'=>    ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'上一级ID' ,        'idx'=>DBIndex_Index ],
        'type'=>        ['type'=>DBField_String,    'len'=>32,  'nullable'=>1,  'cmt'=>'类型' ],

        'description'=> ['type'=>DBField_String,    'len'=>255, 'nullable'=>1,  'cmt'=>'描述' ],
        'cover'=>       ['type'=>DBField_String,    'len'=>255, 'nullable'=>1,  'cmt'=>'封面' ],

        'status'=>      ['type'=>DBField_String,    'len'=>12,  'nullable'=>0,  'cmt'=>'状态 ',   'dft'=>'enabled',      ],

        'createtime'=>  ['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'创建时间',           'idx'=>DBIndex_Index, ],
        'lasttime'=>    ['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'上一次更新时间', ],
        'featured'=>    ['type'=>DBField_Boolean,  'len'=>1,  'nullable'=>0,  'cmt'=>'置顶',  'dft'=>0,    'idx'=>DBIndex_Index, ],
        'sort'=>        ['type'=>DBField_Int,      'len'=>5,  'nullable'=>0,  'cmt'=>'优先排序','dft'=>0,   'idx'=>DBIndex_Index, ],

    ];

    /**w
     * 查询子分类
     * Get child category list
     * @param string $uid
     * @param int $page
     * @param int $size
     * @param null $sort
     * @param DBConditions|null $moreFilters
     * @return ASResult
     */
    public function listChild(string $uid, int $page = 1, int $size = 50, $sort = null, DBConditions $moreFilters = null ):ASResult{

        $moreFilters = $moreFilters ?? DBConditions::init(static::table);
        $moreFilters->and('parentid')->equal($uid);

        return $this->list($moreFilters,$page,$size,$sort);
    }

    /**
     * 查询子分类 计数
     * count Child category
     * @param string $uid
     * @param DBConditions|null $moreFilters
     * @return ASResult
     */
    public function countChild( string $uid, DBConditions $moreFilters = null ):ASResult{

        $moreFilters = $moreFilters ?? DBConditions::init(static::table);
        $moreFilters->and('parentid')->equal($uid);

        return $this->count($moreFilters);
    }

}