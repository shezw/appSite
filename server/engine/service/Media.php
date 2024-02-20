<?php

namespace APS;

/**
 * 媒体管理 基于OSS
 * Media Management( Based on AliyunOSS )
 * @package APS\service
 */
class Media extends ASModel{


    const table     = "item_media";
    const comment   = "通用媒体";
    const primaryid = "uid";
    const alias     = 'media';

    const addFields = [
        'uid', 'categoryid', 'authorid',
        'type','server', 'url', 'size', 'meta',
        'password',
        'sort', 'featured', 'status',
    ];
    const updateFields = [
        'authorid', 'categoryid',
        'type','server', 'url', 'size', 'meta',
        'password',
        'sort', 'featured', 'status',
    ];
    const detailFields = [
        'uid', 'categoryid', 'authorid',
        'type','server', 'url', 'size', 'meta',
        'password',
        'sort', 'featured', 'status',
        'createtime', 'lasttime',
    ];
    const overviewFields = [
        'uid', 'categoryid', 'authorid',
        'type','server', 'url', 'size', 'meta',
        'password',
        'sort', 'featured', 'status',
        'createtime', 'lasttime',
    ];
    const listFields = [
        'uid', 'categoryid', 'authorid',
        'type','server', 'url', 'size', 'meta',
        'password',
        'sort', 'featured', 'status',
        'createtime', 'lasttime',
    ];
    const filterFields = [
        'uid', 'categoryid', 'authorid',
        'type','server', 'url',
        'sort', 'featured', 'status',
        'createtime', 'lasttime',
    ];
    const depthStruct = [
        'server'=>DBField_Int,
        'size'=>DBField_Int,
        'sort'=>DBField_Int,
        'featured'=>DBField_Boolean,
        'meta'=>DBField_Json,
        'createtime'=>DBField_TimeStamp,
        'lasttime'=>DBField_TimeStamp,
    ];

    const tableStruct = [

        'uid'=>         ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'图像ID',  'idx'=>DBIndex_Unique ],
        'saasid'=>      ['type'=>DBField_String,  'len'=>8,   'nullable'=>1,    'cmt'=>'所属saas',  'idx'=>DBIndex_Index,],
        'categoryid'=>  ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'分类ID',  'idx'=>DBIndex_Index ],
        'authorid'=>    ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'所有者 ',  'idx'=>DBIndex_Index ],

        // 'permission'=>   ['type'=>DBFieldType_INT,   'len'=>5,   'nullable'=>0,  'cmt'=>'权限需求',  'dft'=>0,       ],

        'type'=>        ['type'=>DBField_String,    'len'=>16,  'nullable'=>1,  'cmt'=>'类型 ',   'idx'=>DBIndex_Index ],
        'server'=>      ['type'=>DBField_Int,      'len'=>5,  'nullable'=>0,  'cmt'=>'存储服务器','dft'=>0, ],
        // StorageLocationAliOSS, StorageLocation_LocalStatic

        'url'=>         ['type'=>DBField_String,    'len'=>255, 'nullable'=>0,  'cmt'=>'url地址'],
        'size'=>        ['type'=>DBField_Int,       'len'=>20,  'nullable'=>0,  'cmt'=>'文件大小',  'dft'=>0,      ],
        'meta'=>        ['type'=>DBField_Json,      'len'=>-1,  'nullable'=>1,  'cmt'=>'元信息 k-v json'],

        'status'=>      ['type'=>DBField_String,    'len'=>12,  'nullable'=>0,  'cmt'=>'状态',  'dft'=>Status_Enabled,       ],

        'password'=>    ['type'=>DBField_String,    'len'=>255, 'nullable'=>1,  'cmt'=>'密码访问'],
        // 密码访问

        'createtime'=>  ['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'创建时间',            'idx'=>DBIndex_Index, ],
        'lasttime'=>    ['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'上一次更新时间', ],
        'featured'=>    ['type'=>DBField_Boolean,  'len'=>1,  'nullable'=>0,  'cmt'=>'置顶',  'dft'=>0,     'idx'=>DBIndex_Index, ],
        'sort'=>        ['type'=>DBField_Int,      'len'=>5,  'nullable'=>0,  'cmt'=>'优先排序','dft'=>0,    'idx'=>DBIndex_Index, ],
    ];
    /**
     * 彻底删除媒体文件
     * Remove media form server && OSS
     * @param  string  $uid  [媒体ID]
     * @return ASResult
     */
    public function delete( string $uid ): ASResult
    {
        $media = $this->detail($uid)->getContent();
        $url = $media['url'];
        $storageLocation = $media['server'];

        switch ( $storageLocation ){

            case StorageLocation_LocalStatic:
                $removeFile = Uploader::common()->removeFile( $url );
                break;
            case StorageLocation_AliOSS:
                $removeFile = AliyunOSS::common()->removeFile($url);
                break;
            default:
                return $this->error( 506, "media storage method not found" );
        }

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


}