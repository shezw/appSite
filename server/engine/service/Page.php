<?php

namespace APS;

/**
 * 页面
 * Page
 * @package APS\service
 */
class Page extends ASModel
{

    public static $table     = "item_page";  // 表
    public static $primaryid = "title";     // 主字段
    public static $addFields = [
        'title',
        'cover',
        'introduce',
        'status',
        'viewtimes',
    ];      // 添加支持字段
    public static $updateFields = [
        'cover',
        'introduce',
        'status',
        'viewtimes',
    ];   // 更新支持字段
    public static $detailFields = "*";   // 详情支持字段
    public static $publicDetailFields = [
        'title',
        'cover',
        'introduce',
        'status',
        'viewtimes',
        'createtime',
        'lasttime',
    ]; // 概览支持字段
    public static $overviewFields = [
        'title',
        'cover',
        'introduce',
        'status',
        'viewtimes',
        'createtime',
        'lasttime',
    ]; // 概览支持字段
    public static $listFields = [
        'title',
        'cover',
        'introduce',
        'status',
        'viewtimes',
        'createtime',
        'lasttime',
    ];     // 列表支持字段
    public static $publicListFields = [
        'title',
        'cover',
        'introduce',
        'status',
        'viewtimes',
        'createtime',
        'lasttime',
    ];     // 开放接口列表支持字段
    public static $countFilters = [
        'title',
        'cover',
        'introduce',
        'status',
        'viewtimes',
        'createtime',
        'lasttime',
    ];
    public static $depthStruct = [
        'viewtimes'=>'int',
    ];

}