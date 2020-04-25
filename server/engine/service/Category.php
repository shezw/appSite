<?php

namespace APS;

/**
 * Description
 * Category
 * @package APS\service
 */
class Category extends ASModel {

    public static $table     = "item_category";  // 表
    public static $primaryid = "categoryid";     // 主字段
    public static $addFields = [
        'categoryid',
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
        'categoryid',
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
        'categoryid',
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
        'categoryid',
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
     * @param  string      $categoryid
     * @param  int         $page
     * @param  int         $size
     * @param  null        $sort
     * @param  array|null  $moreFilters
     * @return \APS\ASResult
     */
    public function listChild( string $categoryid, $page = 1, $size = 50, $sort = null, array $moreFilters = null ){

        $moreFilters = $moreFilters ?? [];
        $moreFilters['parentid'] = $categoryid;

        return $this->list($moreFilters,$page,$size,$sort);
    }

    /**
     * 查询子分类 计数
     * count Child category
     * @param  string      $categoryid
     * @param  array|null  $moreFilters
     * @return \APS\ASResult
     */
    public function countChild( string $categoryid, array $moreFilters = null ){

        $moreFilters = $moreFilters ?? [];
        $moreFilters['parentid'] = $categoryid;

        return $this->count($moreFilters);
    }

}