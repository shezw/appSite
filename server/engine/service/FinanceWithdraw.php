<?php

namespace APS;

/**
 * 财务 - 提现
 * FinanceWithdraw
 * @package APS\service
 */
class FinanceWithdraw extends ASModel{

    public static $table     = "finance_withdraw";  // 表
    public static $primaryid = "withdrawid";     // 主字段
    public static $addFields = [
        'withdrawid',
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
        'withdrawid',
        'userid',
        'type',
        'target',
        'amount',
        'status',
        'callback',
    ];   // 详情支持字段
    public static $publicDetailFields = [
        'withdrawid',
        'userid',
        'type',
        'target',
        'amount',
        'status',
    ];   // 详情支持字段
    public static $overviewFields = [
        'withdrawid',
        'userid',
        'type',
        'target',
        'amount',
        'status',
    ]; // 概览支持字段
    public static $listFields = [
        'withdrawid',
        'userid',
        'type',
        'target',
        'amount',
        'status',
    ];     // 列表支持字段
    public static $publicListFields = [
        'withdrawid',
        'userid',
        'type',
        'target',
        'amount',
        'status',
    ];     // 开放接口列表支持字段
    public static $countFilters = [
        'withdrawid',
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