<?php

namespace APS;

/**
 * 表单 - 认证
 *
 * @package APS\service
 */
class FormVerify extends ASModel{

    const table     = "form_verify";
    const comment   = '表单-认证';
    const primaryid = "uid";
    const addFields = [
        'uid', 'saasid','userid','itemtype', 'itemid',
        'title','description','cover','expire','attachments',
        'status', 'createtime', 'lasttime',
    ];
    const updateFields = [
        'uid','userid','itemtype', 'itemid',
        'title','description','cover','expire','attachments',
        'status', 'createtime', 'lasttime',
    ];
    const detailFields = [
        'uid', 'saasid','userid','itemtype', 'itemid',
        'title','description','cover','expire','attachments',
        'status', 'createtime', 'lasttime',
    ];
    const overviewFields = [
        'uid', 'saasid','userid','itemtype', 'itemid',
        'title','description','cover','expire',
        'status', 'createtime', 'lasttime',
    ];
    const listFields = [
        'uid', 'saasid','userid','itemtype', 'itemid',
        'title','description','cover','expire',
        'status', 'createtime', 'lasttime',
    ];
    const filterFields = [
        'uid', 'saasid', 'userid', 'itemtype', 'itemid',
        'expire','status',
        'createtime', 'lasttime',
    ];
    const depthStruct = [
        'attachments'=>DBField_Json,
        'expire'=>DBField_TimeStamp,
        'createtime'=>DBField_TimeStamp,
        'lasttime'=>DBField_TimeStamp,
    ];

    const tableStruct = [

        'uid'=>         ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'索引ID' , 'idx'=>DBIndex_Unique ],

        'saasid'=>      ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'所属saas',  'idx'=>DBIndex_Index,],
        'userid'=>      ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'用户ID' , 'idx'=>DBIndex_Index ],
        'itemid'=>      ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'目标ID' , 'idx'=>DBIndex_Index ],
        'itemtype'=>    ['type'=>DBField_String,    'len'=>32,  'nullable'=>1,  'cmt'=>'目标类别' , 'idx'=>DBIndex_Index ],

        'title'=>       ['type'=>DBField_String,    'len'=>64,  'nullable'=>0,  'cmt'=>'名称' ],
        'description'=> ['type'=>DBField_String,    'len'=>256, 'nullable'=>1,  'cmt'=>'描述' ],
        'cover'=>       ['type'=>DBField_String,    'len'=>256, 'nullable'=>1,  'cmt'=>'封面' ],
        'attachments'=> ['type'=>DBField_Json,      'len'=>-1,  'nullable'=>1,  'cmt'=>'附件'],
        'expire'=>      ['type'=>DBField_TimeStamp, 'len'=>13,  'nullable'=>0,  'cmt'=>'过期时间 时间戳' ,     'dft'=>0,       ],
        'status'=>      ['type'=>DBField_String,    'len'=>12,  'nullable'=>0,  'dft'=>'pending',       'cmt'=>'状态' ],

        'createtime'=>  ['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'创建时间',    'idx'=>DBIndex_Index, ],
        'lasttime'=>    ['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'上一次更新时间', ],

        ];
}