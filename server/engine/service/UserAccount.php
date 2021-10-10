<?php

namespace APS;

class UserAccount extends ASModel {

    const table     = "user_account";
    const comment   = '用户-账户';
    const primaryid = 'uid';
    const addFields = [
        'uid','saasid','username','password','email','mobile',
        'nickname','avatar','cover','description','introduce',
        'birthday','gender','groupid','areaid','status',
    ];
    const updateFields = [
        'password','email','mobile',
        'nickname','avatar','cover','description','introduce',
        'birthday','gender','groupid','areaid','status',
    ];
    const detailFields = [
        'uid','saasid','username','email','mobile','password',
        'nickname','avatar','cover','description','introduce',
        'birthday','gender','groupid','areaid','status','createtime','lasttime'
    ];
    const publicDetailFields = [
        'uid','username','email','mobile','saasid',
        'nickname','avatar','cover','description','introduce',
        'birthday','gender','groupid','areaid','status','createtime','lasttime'
    ];
    const overviewFields = [
        'uid','username','saasid',
        'nickname','avatar','description','introduce',
        'groupid','areaid','createtime','lasttime'
    ];
    const listFields = [
        'uid','username','email','mobile','saasid',
        'nickname','avatar','cover','description',
        'groupid','gender','areaid','status','createtime','lasttime'
    ];
    const publicListFields = [
        'uid','username','saasid',
        'nickname','avatar','description',
        'groupid','gender','areaid','status','createtime','lasttime'
    ];

    const filterFields = [
        'uid','username','email','mobile','saasid',
        'nickname','gender','groupid','areaid','status','createtime','lasttime'
    ];

    const depthStruct = [
        'birthday'=>DBField_TimeStamp,
        'createtime'=>DBField_TimeStamp,
        'lasttime'=>DBField_TimeStamp,
    ];

    const tableStruct = [

        'uid'          =>['type'=>DBField_String,  'len'=>8,   'nullable'=>0,                     'cmt'=>'用户ID 唯一索引',     'idx'=>DBIndex_Unique, ],
        'groupid'      =>['type'=>DBField_String,  'len'=>8,   'nullable'=>0,  'dft'=>'100',      'cmt'=>'用户分组 参考user_group'],
        'areaid'       =>['type'=>DBField_String,  'len'=>8,   'nullable'=>0,  'dft'=>'1',        'cmt'=>'地区id',    'idx'=>DBIndex_Index,],
        'saasid'       =>['type'=>DBField_String,  'len'=>8,   'nullable'=>1,                     'cmt'=>'所属saas',  'idx'=>DBIndex_Index,],

        'username'     =>['type'=>DBField_String,  'len'=>63,  'nullable'=>1,                     'cmt'=>'用户名 账号密码登陆用', 'idx'=>DBIndex_Unique, ],
        'password'     =>['type'=>DBField_String,  'len'=>255, 'nullable'=>1,                     'cmt'=>'密码 hash salt加密'],
        'email'        =>['type'=>DBField_String,  'len'=>63,  'nullable'=>1,                     'cmt'=>'邮箱 唯一',           'idx'=>DBIndex_Unique, ],
        'mobile'       =>['type'=>DBField_String,  'len'=>24,  'nullable'=>1,                     'cmt'=>'手机 唯一',           'idx'=>DBIndex_Unique, ],
        'nickname'     =>['type'=>DBField_String,  'len'=>63,  'nullable'=>1,                     'cmt'=>'昵称 30字以内'],
        'avatar'       =>['type'=>DBField_String,  'len'=>255, 'nullable'=>1,                     'cmt'=>'头像 url'],
        'cover'        =>['type'=>DBField_String,  'len'=>255, 'nullable'=>1,                     'cmt'=>'封面 url'],
        'description'  =>['type'=>DBField_String,  'len'=>255, 'nullable'=>1,                     'cmt'=>'介绍 250字以内'],
        'introduce'    =>['type'=>DBField_RichText,'len'=>-1,  'nullable'=>1,                     'cmt'=>'简介 120字以内'],
        'birthday'     =>['type'=>DBField_Int,     'len'=>11,  'nullable'=>1,                     'cmt'=>'生日 时间戳'],
        'gender'       =>['type'=>DBField_String,  'len'=>16,  'nullable'=>0,  'dft'=>'private',  'cmt'=>'性别 female male private'],

        'status'       =>['type'=>DBField_String,  'len'=>12,  'nullable'=>0,  'dft'=>'enabled',  'cmt'=>'状态 enabled 可以 '],

        'createtime'   =>['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,                     'cmt'=>'创建时间',   'idx'=>DBIndex_Index, ],
        'lasttime'     =>['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,                     'cmt'=>'上一次更新时间', ],

    ];



}
