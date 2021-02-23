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

    public static $table     = "user_info";
    public static $primaryid = "userid";
    public static $addFields = [
        'userid','vip','vipexpire',
        'gallery',
        'realname','idnumber',
        'country','province','city','company',
        'wechatid','weiboid','qqid','appleUUID','deviceID',
        'status','realstatus',
    ];
    public static $updateFields = [
        'vip','vipexpire',
        'gallery',
        'realname','idnumber',
        'country','province','city','company',
        'wechatid','weiboid','qqid','appleUUID','deviceID',
        'status','realstatus',
    ];
    public static $detailFields = [
        'userid','vip','vipexpire',
        'gallery',
        'realname','idnumber',
        'country','province','city','company',
        'wechatid','weiboid','qqid','appleUUID','deviceID',
        'status','realstatus',
    ];
    public static $publicDetailFields = [
        'userid','vip','vipexpire',
        'gallery',
        'realname','idnumber',
        'country','province','city','company','deviceID',
        'status','realstatus',
    ];
    public static $overviewFields = [
        'userid','vip','vipexpire',
        'gallery',
        'realname','idnumber',
        'country','province','city','company',
        'status','realstatus',
    ];
    public static $listFields = [
        'userid','vip','vipexpire',
        'realname','idnumber',
        'country','province','city','company',
        'wechatid','weiboid','qqid','appleUUID','deviceID',
        'status','realstatus',
    ];
    public static $publicListFields = [
        'userid','vip','vipexpire',
        'realname','idnumber',
        'country','province','city','company',
        'status','realstatus',
    ];
    public static $countFilters = [
        'userid','vip','vipexpire',
        'realname','idnumber',
        'country','province','city','company',
        'wechatid','weiboid','qqid','appleUUID','deviceID',
        'status','realstatus',
    ];
    public static $depthStruct = [
        'createtime'=>'int',
        'lasttime'=>'int',
        'vip'=>'int',
        'vipexpire'=>'int',
        'gallery'=>'ASJson',
    ];

    function __construct( string $userid = null ){

        parent::__construct(true);
        $this->userid = $userid;
    }

    /**
     * 初始化用户信息
     * init
     * @param  array  $params
     * @return ASResult
     */
    public function init( array $params ):ASResult {

        $params['userid'] = $this->userid;
        return $this->add($params);
    }

}

