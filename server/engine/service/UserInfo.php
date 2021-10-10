<?php

namespace APS;

/**
 * 用户信息扩展
 * UserInfo
 * @mark
        - userid       用户ID 唯一索引
        - gallery      相册 JSON ARRAY
        - vip          是否vip
        - vipexpire    vip过期时间
        - realname     真实姓名 30字以内
        - idnumber     身份证号 30字以内
        - country      国家 12字以内
        - province     省份 12字以内
        - city         城市 12字以内
        - company      公司 30字以内
        - wechatid     微信公众平台openid 默认获取 unionid
        - weiboid      微博ID
        - appleUUID    苹果UUID
        - qqid         qqID
        - deviceID     设备ID     用于推送等功能
        - status       状态 enabled
        - realstatus   实名状态
 * @package APS\service\User
 */
class UserInfo extends ASModel{

    /**
     * 所属用户id
     * @var string
     */
    public $userid;

    const table     = "user_info";
    const comment   = '用户-信息';
    const primaryid = "userid";
    const addFields = [
        'userid','vip','vipexpire',
        'gallery',
        'realname','idnumber',
        'country','province','city','company',
        'wechatid','weiboid','qqid','appleUUID','deviceID',
        'status','realstatus',
    ];
    const updateFields = [
        'vip','vipexpire',
        'gallery',
        'realname','idnumber',
        'country','province','city','company',
        'wechatid','weiboid','qqid','appleUUID','deviceID',
        'status','realstatus',
    ];
    const detailFields = [
        'userid','vip','vipexpire',
        'gallery',
        'realname','idnumber',
        'country','province','city','company',
        'wechatid','weiboid','qqid','appleUUID','deviceID',
        'status','realstatus',
    ];
    const publicDetailFields = [
        'userid','vip','vipexpire',
        'gallery',
        'realname','idnumber',
        'country','province','city','company','deviceID',
        'status','realstatus',
    ];
    const overviewFields = [
        'userid','vip','vipexpire',
        'gallery',
        'realname','idnumber',
        'country','province','city','company',
        'status','realstatus',
    ];
    const listFields = [
        'userid','vip','vipexpire',
        'realname','idnumber',
        'country','province','city','company',
        'wechatid','weiboid','qqid','appleUUID','deviceID',
        'status','realstatus',
    ];
    const publicListFields = [
        'userid','vip','vipexpire',
        'realname','idnumber',
        'country','province','city','company',
        'status','realstatus',
    ];
    const filterFields = [
        'userid','vip','vipexpire',
        'realname','idnumber',
        'country','province','city','company',
        'wechatid','weiboid','qqid','appleUUID','deviceID',
        'status','realstatus',
    ];
    const depthStruct = [
        'createtime'=>DBField_TimeStamp,
        'lasttime'=>DBField_TimeStamp,
        'vip'=>DBField_Int,
        'vipexpire'=>DBField_TimeStamp,
        'gallery'=>DBField_Json,
    ];

    const tableStruct = [
        'userid'=>      ['type'=>DBField_String,  'len'=>8,    'nullable'=>0,  'cmt'=>'用户ID 唯一索引','idx'=>DBIndex_Unique, ],
        'gallery'=>     ['type'=>DBField_Json,    'len'=>-1,   'nullable'=>1,  'cmt'=>'相册 JSON ARRAY'],

        'vip'=>         ['type'=>DBField_Int,     'len'=>2,    'nullable'=>0,  'cmt'=>'是否vip',      'dft'=>0,  'idx'=>DBIndex_Index,  ],
        'vipexpire'=>   ['type'=>DBField_Int,     'len'=>11,   'nullable'=>0,  'cmt'=>'vip过期时间',   'dft'=>0,  'idx'=>DBIndex_Index,  ],

        'realname'=>    ['type'=>DBField_String,  'len'=>63,   'nullable'=>1,  'cmt'=>'真实姓名 30字以内'],
        'idnumber'=>    ['type'=>DBField_String,  'len'=>63,   'nullable'=>1,  'cmt'=>'身份证号 30字以内'],
        'country'=>     ['type'=>DBField_String,  'len'=>24,   'nullable'=>1,  'cmt'=>'国家 12字以内'],
        'province'=>    ['type'=>DBField_String,  'len'=>24,   'nullable'=>1,  'cmt'=>'省份 12字以内'],
        'city'=>        ['type'=>DBField_String,  'len'=>24,   'nullable'=>1,  'cmt'=>'城市 12字以内'],
        'company'=>     ['type'=>DBField_String,  'len'=>63,   'nullable'=>1,  'cmt'=>'公司 30字以内'],

        'wechatid'=>    ['type'=>DBField_String,  'len'=>32,   'nullable'=>1,  'cmt'=>'微信公众平台openid 默认获取 unionid', 'idx'=>DBIndex_Unique, ],
        'weiboid'=>     ['type'=>DBField_String,  'len'=>63,   'nullable'=>1,  'cmt'=>'微博ID'],
        'appleUUID'=>   ['type'=>DBField_String,  'len'=>64,   'nullable'=>1,  'cmt'=>'苹果UUID'],
        'qqid'=>        ['type'=>DBField_String,  'len'=>63,   'nullable'=>1,  'cmt'=>'qqID'],
        'deviceid'=>    ['type'=>DBField_String,  'len'=>64,   'nullable'=>1,  'cmt'=>'Device ID'],
        'status'=>      ['type'=>DBField_String,  'len'=>12,   'nullable'=>0,  'cmt'=>'状态 enabled'  , 'dft'=>Status_Enabled, ],
        'realstatus'=>  ['type'=>DBField_String,  'len'=>24,   'nullable'=>0,  'cmt'=>'实名状态 '  ,    'dft'=>Status_Default, ],

        'createtime'=>  ['type'=>DBField_TimeStamp,'len'=>13,  'nullable'=>0,  'cmt'=>'创建时间',                      'idx'=>DBIndex_Index, ],
        'lasttime'=>    ['type'=>DBField_TimeStamp,'len'=>13,  'nullable'=>0,  'cmt'=>'上一次更新时间', ],
    ];


    function __construct( string $userid = null ){

        parent::__construct();
        $this->userid = $userid;
    }

    /**
     * 初始化用户信息
     * init
     * @param DBValues $data
     * @return ASResult
     */
    public function init( DBValues $data ):ASResult {

        $data->set(static::primaryid)->string( $this->userid );

        return $this->add($data);
    }

}

