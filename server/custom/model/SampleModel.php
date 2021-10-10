<?php

namespace APS;

/**
 * 一个模型的示范类
 * A Model Class sample
 * @package APS\custom\model
 */
class SampleModel extends ASModel{

    /**
     * 数据表名
     * @var string
     */
    const table = "item_sample";

    const comment = "示例模型类";

    /**
     * 主索引字段
     * @var string
     */
    const primaryid = 'uid';

    /**
     * 添加支持字段
     * @var array [string]
     */
    const addFields = [];

    /**
     * 更新支持字段
     * @var array [string]
     */
    const updateFields = [];

    /**
     * 详情支持字段
     * @var array [string]
     */
    const detailFields = [];

    /**
     * 外部接口详情支持字段
     * @var array [string]
     */
    const publicDetailFields = [];

    /**
     * 概览支持字段
     * @var array [string]
     */
    const overviewFields = [];

    /**
     * 列表支持字段
     * @var array [string]
     */
    const listFields = [];

    /**
     * 外部接口列表支持字段
     * @var array [string]
     */
    const publicListFields = [];

    /**
     * 计数、查询筛选 支持字段 ( 原countFilters )
     * @var array [string]
     */
    const filterFields = [];

    /**
     * 数据转换规则
     * @var array
     */
    const depthStruct = [
        'createtime'=>DBField_TimeStamp,
        'lasttime'=>DBField_TimeStamp,
    ];

    /**
     * @var array (name=>[properties])
     */
    const tableStruct = [

        'uid'=>         ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'主id' , 'idx'=>DBIndex_Unique ],
        'userid' =>     ['type'=>DBField_String,    'len'=>8,    'nullable'=>0,     'cmt'=>'用户ID', 'idx'=>DBIndex_Index,],

        'status'=>      ['type'=>DBField_String,    'len'=>12,  'nullable'=>0,  'cmt'=>'状态',    'dft'=>Status_Enabled, ],

        'createtime'=>  ['type'=>DBField_TimeStamp, 'len'=>13, 'nullable'=>0,  'cmt'=>'创建时间',          'idx'=>DBIndex_Index, ],
        'lasttime'=>    ['type'=>DBField_TimeStamp, 'len'=>13, 'nullable'=>0,  'cmt'=>'上一次更新时间',                           ],
    ];

}