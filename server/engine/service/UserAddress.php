<?php

namespace APS;

/**
 * 用户地址扩展
 * UserAddress
 *
 * @package APS\service\User
 */
class UserAddress extends ASModel{

    /**
     * 所属用户id
     * @var string
     */
    public $userid;

    const table     = "user_address";
    const comment   = '用户-地址';
    const addFields = [
        'userid','type',
        'email',
        'country','state','city',
        'address','firstname','lastname','zip','phone',
        'status','featured','createtime','lasttime',
    ];
    const updateFields = [
        'email',
        'country','state','city',
        'address','firstname','lastname','zip','phone',
        'status','featured','createtime','lasttime',
    ];
    const detailFields =[
        'uid','userid','type',
        'email',
        'country','state','city',
        'address','firstname','lastname','zip','phone',
        'status','featured','createtime','lasttime',
    ];
    const publicDetailFields = [
        'uid','userid','type',
        'email',
        'country','state','city',
        'address','firstname','lastname','zip','phone',
        'status','featured','createtime','lasttime',
    ];
    const overviewFields = [
        'uid','userid','type',
        'email',
        'country','state','city',
        'address','firstname','lastname','zip','phone',
        'status','featured','createtime','lasttime',
    ]; // 概览支持字段
    const listFields = [
        'uid','userid','type',
        'email',
        'country','state','city',
        'address','firstname','lastname','zip','phone',
        'status','featured','createtime','lasttime',
    ];
    const publicListFields = [
        'uid','userid','type',
        'email',
        'country','state','city',
        'address','firstname','lastname','zip','phone',
        'status','featured','createtime','lasttime',
    ];
    const filterFields = [
        'uid','userid','type',
        'email',
        'country','state','city',
        'address','firstname','lastname','zip','phone',
        'status','featured','createtime','lasttime',
    ];
    const depthStruct = [
        'featured'=>DBField_Boolean,
        'createtime'=>DBField_TimeStamp,
        'lasttime'=>DBField_TimeStamp
    ];

    const tableStruct = [

        'uid'=>        ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'核销ID',  'idx'=>DBIndex_Unique ],
        'userid'=>     ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'用户ID',  'idx'=>DBIndex_Index ],
        'type'=>       ['type'=>DBField_String,    'len'=>16,  'nullable'=>1,  'cmt'=>'类型 ',   'idx'=>DBIndex_Index ],

        'country'=>    ['type'=>DBField_String,    'len'=>32,  'nullable'=>1,  'cmt'=>'国家',   ],
        'state'=>      ['type'=>DBField_String,    'len'=>32,  'nullable'=>1,  'cmt'=>'州/省',  ],
        'city'=>       ['type'=>DBField_String,    'len'=>32,  'nullable'=>1,  'cmt'=>'城市',  ],

        'address'=>    ['type'=>DBField_String,    'len'=>256, 'nullable'=>1,  'cmt'=>'地址详情'],
        'lastname'=>   ['type'=>DBField_String,    'len'=>32,  'nullable'=>1,  'cmt'=>'姓',  ],
        'firstname'=>  ['type'=>DBField_String,    'len'=>32,  'nullable'=>1,  'cmt'=>'名',   ],
        'zip'=>        ['type'=>DBField_String,    'len'=>16,  'nullable'=>1,  'cmt'=>'邮编 ',  ],

        'email'=>      ['type'=>DBField_String,    'len'=>64, 'nullable'=>1,  'cmt'=>'邮箱'],
        'phone'=>      ['type'=>DBField_String,    'len'=>32, 'nullable'=>1,  'cmt'=>'手机号'],

        'status'=>     ['type'=>DBField_String,    'len'=>12,  'nullable'=>0,  'cmt'=>'状态',    'dft'=>'enabled', ],

        'createtime'=> ['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'创建时间',             'idx'=>DBIndex_Index, ],
        'lasttime'=>   ['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'上一次更新时间', ],
        'featured'=>   ['type'=>DBField_Boolean,  'len'=>1,  'nullable'=>0,  'cmt'=>'置顶',  'dft'=>0,     'idx'=>DBIndex_Index, ],
        'sort'=>       ['type'=>DBField_Int,      'len'=>5,  'nullable'=>0,  'cmt'=>'优先排序','dft'=>0,    'idx'=>DBIndex_Index, ],
    ];

}