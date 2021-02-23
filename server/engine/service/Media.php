<?php

namespace APS;

/**
 * 媒体管理 基于OSS
 * Media Management( Based on AliyunOSS )
 * @package APS\service
 */
class Media extends ASModel{


    /**
     * 彻底删除媒体文件
     * Remove media form server && OSS
     * @param  string  $uid  [媒体ID]
     * @return ASResult
     */
    public function delete( string $uid ): ASResult
    {

        $url = $this->detail($uid)->getContent()['url'];

        $removeFile = AliyunOSS::common()->removeFile($url);
        /*
        当前直接移除 阿里云oss文件 未来考虑更多平台接入
         */

        if(!$removeFile->isSucceed()){ return $removeFile; }

        return $this->remove($uid);
    }


    /**
     * 查询链接地址
     * Get media URL by uid
     * @param  string  $uid  媒体ID
     * @return ASResult
     */
    public function getUrl( string $uid ): ASResult
    {

        $getDetail = $this->detail($uid);

        if(!$getDetail->isSucceed()){ return $getDetail; }

        return $this->take($getDetail->getContent()['url'])->success();
    }


    public static $table     = "item_media";  // 表
    public static $primaryid = "uid";     // 主字段
    public static $addFields = [
        'uid', 'categoryid', 'authorid',
        'type', 'url', 'size', 'meta',
        'password',
        'sort', 'featured', 'status',
    ];      // 添加支持字段
    public static $updateFields = [
        'authorid', 'categoryid',
        'type', 'url', 'size', 'meta',
        'password',
        'sort', 'featured', 'status',
    ];   // 更新支持字段
    public static $detailFields = "*";   // 详情支持字段
    public static $overviewFields = [
        'uid', 'categoryid', 'authorid',
        'type', 'url', 'size', 'meta',
        'password',
        'sort', 'featured', 'status',
        'createtime', 'lasttime',
    ]; // 概览支持字段
    public static $listFields = [
        'uid', 'categoryid', 'authorid',
        'type', 'url', 'size', 'meta',
        'password',
        'sort', 'featured', 'status',
        'createtime', 'lasttime',
    ];     // 列表支持字段
    public static $countFilters = [
        'uid', 'categoryid', 'authorid',
        'type', 'url',
        'sort', 'featured', 'status',
        'createtime', 'lasttime',
    ];
    public static $depthStruct = [
        'size'=>'int',
        'sort'=>'int',
        'featured'=>'int',
        'meta'=>'ASJson',
        'createtime'=>'int',
        'lasttime'=>'int',
    ];

}