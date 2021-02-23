<?php

namespace APS;

/**
 * Description
 * Category
 * @package APS\service
 */
class Category extends ASModel {

    public static $table     = "item_category";  // 表
    public static $primaryid = "uid";     // 主字段
    public static $addFields = [
        'uid',
        'authorid',
        'parentid',
        'type',
        'title',
        'cover',
        'description',
        'sort',
        'featured',
        'status',
    ];      // 添加支持字段
    public static $updateFields = [
        'authorid',
        'parentid',
        'type',
        'title',
        'cover',
        'description',
        'sort',
        'featured',
        'status',
    ];   // 更新支持字段
    public static $detailFields = "*";   // 详情支持字段
    public static $overviewFields = [
        'uid',
        'authorid',
        'parentid',
        'type',
        'title',
        'cover',
        'description',
        'sort',
        'featured',
        'status',
        'createtime',
        'lasttime',
    ]; // 概览支持字段
    public static $listFields = [
        'uid',
        'authorid',
        'parentid',
        'type',
        'title',
        'cover',
        'description',
        'sort',
        'featured',
        'status',
        'createtime',
        'lasttime',
    ];     // 列表支持字段
    public static $countFilters = [
        'uid',
        'authorid',
        'parentid',
        'type',
        'title',
        'sort',
        'featured',
        'status',
        'createtime',
        'lasttime',
    ];
    public static $depthStruct = [
        'sort'=>'int',
        'featured'=>'int',
        'createtime'=>'int',
        'lasttime'=>'int',
    ];

    /**w
     * 查询子分类
     * Get child category list
     * @param  string      $uid
     * @param  int         $page
     * @param  int         $size
     * @param  null        $sort
     * @param  array|null  $moreFilters
     * @return ASResult
     */
    public function listChild( string $uid, $page = 1, $size = 50, $sort = null, array $moreFilters = null ):ASResult{

        $moreFilters = $moreFilters ?? [];
        $moreFilters['parentid'] = $uid;

        return $this->list($moreFilters,$page,$size,$sort);
    }

    /**
     * 查询子分类 计数
     * count Child category
     * @param  string      $uid
     * @param  array|null  $moreFilters
     * @return ASResult
     */
    public function countChild( string $uid, array $moreFilters = null ):ASResult{

        $moreFilters = $moreFilters ?? [];
        $moreFilters['parentid'] = $uid;

        return $this->count($moreFilters);
    }

}