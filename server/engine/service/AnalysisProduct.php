<?php
/**
 * AnalysisProduct.php
 *
 * Description
 *
 *
 */

namespace APS;


class AnalysisProduct extends ASModel
{

    public static $table     = "analysis_product";  // 表
    public static $primaryid = "uid";     // 主字段
    public static $addFields = [
        'uid',
        'userid',
        'cover',
        'title',
        'price',
        'sale',
        'count',
        'total',
    ];      // 添加支持字段
    public static $updateFields = [
        'uid',
        'userid',
        'cover',
        'title',
        'price',
        'sale',
        'count',
        'total',
    ];   // 更新支持字段
    public static $detailFields = "*";   // 详情支持字段
    public static $overviewFields = [
        'uid',
        'userid',
        'cover',
        'title',
        'price',
        'sale',
        'count',
        'total',
        'createtime',
        'lasttime',
    ]; // 概览支持字段
    public static $listFields = [
        'uid',
        'userid',
        'cover',
        'title',
        'price',
        'sale',
        'count',
        'total',
        'createtime',
        'lasttime',
    ];     // 列表支持字段
    public static $countFilters = [
        'uid',
        'userid',
        'cover',
        'title',
        'price',
        'sale',
        'count',
        'total',
        'createtime',
        'lasttime',
    ];
    public static $depthStruct = [
        'createtime'=>'int',
        'lasttime'=>'int',
        'price'=>'double',
        'sale'=>'double',
        'count'=>'int',
        'total'=>'double'
    ];



}