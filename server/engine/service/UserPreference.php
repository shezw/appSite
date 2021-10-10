<?php

namespace APS;

/**
 * 个人偏好
 * UserPreference
 *
 * @package APS\service\User
 */
class UserPreference extends ASModel{

    const table     = "user_preference";
    const comment   = '用户-个人偏好';
    const primaryid = "uid";
    const addFields = [
        'uid','saasid','userid','keyid',
        'description','content',
        'version','status',
        'createtime','lasttime','featured','sort'
    ];
    const updateFields = [
        'userid','keyid',
        'description','content',
        'version','status',
        'createtime','lasttime','featured','sort'
    ];
    const detailFields =[
        'uid','saasid','userid','keyid',
        'description','content',
        'version','status',
        'createtime','lasttime','featured','sort'
    ];
    const publicDetailFields = [
        'uid','saasid','userid','keyid',
        'description','content',
        'version','status',
        'createtime','lasttime','featured','sort'
    ];
    const overviewFields = [
        'uid','saasid','userid','keyid',
        'description','content',
        'version','status',
        'createtime','lasttime','featured','sort'
    ]; // 概览支持字段
    const listFields = [
        'uid','saasid','userid','keyid',
        'description','content',
        'version','status',
        'createtime','lasttime','featured','sort'
    ];
    const publicListFields = [
        'uid','saasid','userid','keyid',
        'description','content',
        'version','status',
        'createtime','lasttime','featured','sort'
    ];
    const filterFields = [
        'uid','userid','saasid','keyid',
        'status','version','createtime','lasttime','sort','featured'
    ];
    const depthStruct = [
        'version'=>DBField_Int,
        'content'=>DBField_Json,
        'featured'=>DBField_Boolean,
        'sort'=>DBField_Int,
        'createtime'=>DBField_TimeStamp,
        'lasttime'=>DBField_TimeStamp,
    ];

    const tableStruct = [

        'uid'=>         ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'主ID' ,      'idx'=>DBIndex_Unique ],
        'saasid'=>      ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'所属saas',   'idx'=>DBIndex_Index,],
        'userid'=>      ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'用户ID' ,    'idx'=>DBIndex_Index ],
        'keyid'=>       ['type'=>DBField_String,    'len'=>16,  'nullable'=>0,  'cmt'=>'查询key' ,    'idx'=>DBIndex_Index ],
        'description'=> ['type'=>DBField_String,    'len'=>255, 'nullable'=>1,  'cmt'=>'描述 120字以内' ],
        'content'=>     ['type'=>DBField_Json,      'len'=>-1,  'nullable'=>0,  'cmt'=>'设置内容 k-v json' ],
        'version'=>     ['type'=>DBField_Int,       'len'=>8,   'nullable'=>0,  'cmt'=>'版本号 整数 ',   'dft'=>1, ],
        'status'=>      ['type'=>DBField_String,    'len'=>12,  'nullable'=>0,  'cmt'=>'状态',    'dft'=>Status_Enabled, ],

        'createtime'=>  ['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'创建时间',           'idx'=>DBIndex_Index, ],
        'lasttime'=>    ['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'上一次更新时间', ],
        'featured'=>    ['type'=>DBField_Boolean,  'len'=>1,  'nullable'=>0,  'cmt'=>'置顶',  'dft'=>0,    'idx'=>DBIndex_Index, ],
        'sort'=>        ['type'=>DBField_Int,      'len'=>5,  'nullable'=>0,  'cmt'=>'优先排序','dft'=>0,   'idx'=>DBIndex_Index, ],

    ];


}