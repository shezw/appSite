<?php

namespace APS;

/**
 * 地区
 * Area
 * @package APS
 */
class Area extends ASModel{

    public function listChild( array $params , string $areaid , int $page = 1, int $size = 15, string $sort = null ){

        $params['parentid'] = $areaid;

        return $this->list($params,$page,$size,$sort);
    }

    // 查询对应的下级
    public function countChild( array $params , string $areaid ){

        $params['parentid'] = $areaid;

        return $this->count($params);
    }

    public function getParents( string $areaid, array $tmp = null ){

        $parents = $tmp ?? [];

        $area = $this->detail($areaid)->getContent();

        if( isset($area['parentid']) ){

            $parents[] = $area['parentid'];
            $parents = $this->getParents( $area['parentid'], $parents );
        }

        return $parents;
    }


    public static $table     = "item_area";  // 表
    public static $primaryid = "areaid";     // 主字段
    public static $addFields = [
        'areaid',
        'authorid',
        'parentid',
        'title',
        'cover',
        'description',
        'gallery',
        'mergename',
        'shortname',
        'mergeshortname',
        'code',
        'zipcode',
        'location',
        'lng',
        'lat',
        'level',
        'sort',
        'featured',
        'status',
    ];      // 添加支持字段
    public static $updateFields = [
        'authorid',
        'parentid',
        'title',
        'cover',
        'description',
        'gallery',
        'mergename',
        'shortname',
        'mergeshortname',
        'code',
        'zipcode',
        'location',
        'lng',
        'lat',
        'level',
        'sort',
        'featured',
        'status',
    ];   // 更新支持字段
    public static $detailFields = "*";   // 详情支持字段
    public static $overviewFields = [
        'areaid',
        'authorid',
        'parentid',
        'title',
        'cover',
        'description',
        'gallery',
        'mergename',
        'shortname',
        'mergeshortname',
        'code',
        'zipcode',
        // 'location',
        'lng',
        'lat',
        'level',
        'sort',
        'featured',
        'status',
        'createtime',
        'lasttime',
    ]; // 概览支持字段
    public static $listFields = [
        'areaid',
        'authorid',
        'parentid',
        'title',
        'cover',
        'description',
        'gallery',
        'mergename',
        'shortname',
        'mergeshortname',
        'code',
        'zipcode',
        // 'location',
        'lng',
        'lat',
        'level',
        'sort',
        'featured',
        'status',
        'createtime',
        'lasttime',
    ];     // 列表支持字段
    public static $countFilters = [
        'areaid',
        'authorid',
        'parentid',
        'title',
        'level',
        'location',
        'sort',
        'featured',
        'status',
        'createtime',
        'lasttime',
    ];
    public static $depthStruct = [
        'level'=>'int',
        'sort'=>'int',
        'featured'=>'int',
        'createtime'=>'int',
        'lasttime'=>'int',
        'gallery'=>'ASJson',
        'lng'=>'double',
        'lat'=>'double',
    ];


}