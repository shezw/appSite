<?php

namespace APS;

/**
 * 用户钱包扩展
 * UserPocket
 *
 * @mark !Notice:
        在钱包功能中不应该包含 账目处理
        账目处理应该先通过钱包方法返回的结果再交由具体的事务单独判断处理
 * @package APS\service\User
 */
class UserAddress extends ASModel{

    /**
     * 所属用户id
     * @var string
     */
    public $userid;

    public static $table     = "user_address";
    public static $primaryid = "uid";
    public static $addFields = [
        'userid',
        'type',
        'email',
        'country',
        'state',
        'city',
        'address',
        'firstname',
        'lastname',
        'zip',
        'phone',
        'status',
        'featured'
    ];
    public static $updateFields = [
        'country',
        'state',
        'city',
        'email',
        'address',
        'firstname',
        'lastname',
        'zip',
        'phone',
        'status',
        'featured'
    ];
    public static $detailFields =[
        'uid',
        'userid',
        'email',
        'country',
        'state',
        'city',
        'address',
        'firstname',
        'lastname',
        'zip',
        'phone',
        'status',
        'featured',
        'createtime',
        'lasttime',
    ];
    public static $publicDetailFields = [
        'uid',
        'userid',
        'country',
        'email',
        'state',
        'city',
        'address',
        'firstname',
        'lastname',
        'zip',
        'phone',
        'status',
        'featured',
        'createtime',
        'lasttime',
    ];
    public static $overviewFields = [
        'uid',
        'userid',
        'email',
        'country',
        'state',
        'city',
        'address',
        'firstname',
        'lastname',
        'zip',
        'phone',
        'status',
        'featured',
        'createtime',
        'lasttime',
    ]; // 概览支持字段
    public static $listFields = [
        'uid',
        'userid',
        'email',
        'country',
        'state',
        'city',
        'address',
        'firstname',
        'lastname',
        'zip',
        'phone',
        'status',
        'featured',
        'createtime',
        'lasttime',
    ];
    public static $publicListFields = [
        'uid',
        'userid',
        'email',
        'country',
        'state',
        'city',
        'address',
        'firstname',
        'lastname',
        'zip',
        'phone',
        'status',
        'featured',
        'createtime',
        'lasttime',
    ];
    public static $countFilters = [
        'uid',
        'userid',
        'email',
        'country',
        'state',
        'city',
        'zip',
        'status',
        'featured',
        'createtime',
        'lasttime',
    ];
    public static $depthStruct = [
        'featured'=>'int',
        'createtime'=>'int',
        'lasttime'=>'int'
    ];

}