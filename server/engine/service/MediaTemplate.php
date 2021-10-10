<?php

namespace APS;

/**
 * 媒体模板 MediaTemplate
 * 用于存储 短信模板、邮件模板等
 * Templates for email,sms
 * @package APS\service
 */
class MediaTemplate extends ASModel{

    const table     = "media_template";
    const comment   = '媒体模板';
    const primaryid = "uid";
    const addFields = [
        'uid', 'categoryid','saasid', 'authorid','keyid',
        'description','type','content', 'status',
        'sort', 'featured',
        'createtime', 'lasttime',
    ];
    const updateFields = [
        'categoryid','keyid',
        'description','type','content', 'status',
        'sort', 'featured',
        'createtime', 'lasttime',
    ];
    const detailFields = [
        'uid', 'categoryid','saasid', 'authorid','keyid',
        'description','type','content','status',
        'sort', 'featured',
        'createtime', 'lasttime',
    ];
    const overviewFields = [
        'uid', 'categoryid','saasid', 'authorid','keyid',
        'description','type', 'status',
        'sort', 'featured',
        'createtime', 'lasttime',
    ];
    const listFields = [
        'uid', 'categoryid','saasid', 'authorid','keyid',
        'description','type', 'status',
        'sort', 'featured',
        'createtime', 'lasttime',
    ];
    const filterFields = [
        'uid', 'categoryid','saasid', 'authorid','keyid',
        'type', 'status',
        'sort', 'featured',
        'createtime', 'lasttime',
    ];
    const depthStruct = [
        'sort'=>DBField_Int,
        'featured'=>DBField_Boolean,
        'createtime'=>DBField_TimeStamp,
        'lasttime'=>DBField_TimeStamp,
    ];

    const tableStruct = [

        'uid'=>         ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'图像ID',  'idx'=>DBIndex_Unique ],
        'saasid'=>      ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'所属saas','idx'=>DBIndex_Index,],
        'categoryid'=>  ['type'=>DBField_String,  'len'=>8,   'nullable'=>1,  'cmt'=>'分类ID',  'idx'=>DBIndex_Index ],
        'authorid'=>    ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'所有者 ',  'idx'=>DBIndex_Index ],

        'type'=>        ['type'=>DBField_String,    'len'=>16,  'nullable'=>0,  'cmt'=>'类型 ',   'idx'=>DBIndex_Index ],
        'i18n'=>        ['type'=>DBField_String,    'len'=>12,  'nullable'=>0,  'cmt'=>'类型 ',   'idx'=>DBIndex_Index ],
        # Type_Email, Type_HTML, Type_SMS

        'keyid'=>       ['type'=>DBField_String,    'len'=>16,  'nullable'=>0,  'cmt'=>'查询key' , 'idx'=>DBIndex_Index ],
        'description'=> ['type'=>DBField_String,    'len'=>255, 'nullable'=>1,  'cmt'=>'描述 120字以内' ],
        'content'=>     ['type'=>DBField_RichText,  'len'=>-1,  'nullable'=>1,  'cmt'=>'模板内容'],

        'status'=>      ['type'=>DBField_String,    'len'=>12,  'nullable'=>0,  'cmt'=>'状态',       'dft'=>Status_Enabled,       ],

        'createtime'=>  ['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'创建时间',          'idx'=>DBIndex_Index, ],
        'lasttime'=>    ['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'上一次更新时间', ],
        'featured'=>    ['type'=>DBField_Boolean,  'len'=>1,  'nullable'=>0,  'cmt'=>'置顶',  'dft'=>0,   'idx'=>DBIndex_Index, ],
        'sort'=>        ['type'=>DBField_Int,      'len'=>5,  'nullable'=>0,  'cmt'=>'优先排序','dft'=>0,  'idx'=>DBIndex_Index, ],
    ];


    public function recent( string $keyId, string $type, string $i18n = null ): ASResult
    {
        $conditions = DBConditions::init(static::table)->where('keyid')->equal($keyId)->and('type')->equal($type)->and('i18n')->equalIf($i18n);
        $getList = static::common()->list( $conditions, 1,1 );

        if( !$getList->isSucceed() ){ return $getList; }

        return $this->take( $getList->getContent()[0] )->success('SYS_GET_SUC','MediaTemplate->recent');
    }

}