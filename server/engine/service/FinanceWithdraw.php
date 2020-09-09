<?php

namespace APS;

/**
 * 财务 - 提现
 * FinanceWithdraw
 * @package APS\service
 */
class FinanceWithdraw extends ASModel{

    public static $table     = "finance_withdraw";  // 表
    public static $primaryid = "uid";     // 主字段
    public static $addFields = [
        'uid',
        'userid',
        'type',
        'target',
        'amount',
        'status',
        'callback',
    ];      // 添加支持字段
    public static $updateFields = [
        'status',
        'callback',
    ];   // 更新支持字段
    public static $detailFields = [
        'uid',
        'userid',
        'type',
        'target',
        'amount',
        'status',
        'callback',
    ];   // 详情支持字段
    public static $publicDetailFields = [
        'uid',
        'userid',
        'type',
        'target',
        'amount',
        'status',
    ];   // 详情支持字段
    public static $overviewFields = [
        'uid',
        'userid',
        'type',
        'target',
        'amount',
        'status',
    ]; // 概览支持字段
    public static $listFields = [
        'uid',
        'userid',
        'type',
        'target',
        'amount',
        'status',
    ];     // 列表支持字段
    public static $publicListFields = [
        'uid',
        'userid',
        'type',
        'target',
        'amount',
        'status',
    ];     // 开放接口列表支持字段
    public static $countFilters = [
        'uid',
        'userid',
        'type',
        'amount',
        'status',
    ];
    public static $depthStruct = [
        'createtime'=>'int',
        'lasttime'=>'int',
        'amount'=>'float',
        'target'=>'ASJson',
        'callback'=>'ASJson',
    ];


}