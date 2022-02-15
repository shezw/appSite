<?php

namespace APS;

/**
 * 文章模块
 * Article
 * @package APS\service
 */
class Article extends ASModel{

    const table     = "item_article";
    const comment   = '文章模块';
    const primaryid = "uid";
    const alias     = 'article';

    const addFields = [
        'uid','categoryid','authorid','areaid','regionid','saasid',
        'type','mode','title','cover','gallery','video','link','tags','description','introduce',
        'viewtimes','sort','featured','status',
        'createtime','lasttime',
    ];

    const updateFields = [
        'categoryid','areaid','regionid','saasid',
        'type','mode','title','cover','gallery','video','link','tags','description','introduce',
        'viewtimes','sort','featured','status','createtime','lasttime'
    ];
    const detailFields  = [
        'uid','categoryid','authorid','areaid','regionid','saasid',
        'type','mode','title','cover','gallery','video','link','tags','description','introduce',
        'viewtimes','sort','featured','status',
        'createtime','lasttime',
    ];
    const overviewFields  = [
        'uid','categoryid','authorid','areaid','regionid','saasid',
        'type','mode','title','cover','gallery','video','link','tags','description','introduce',
        'viewtimes','sort','featured','status','createtime','lasttime'
    ];
    const listFields  = [
        'uid','categoryid','authorid','areaid','regionid','saasid',
        'type','mode','title','cover','description','link','tags',
        'viewtimes','sort','featured','status','createtime','lasttime'
    ];
    const filterFields = [
        'uid','categoryid','authorid','areaid','regionid','saasid',
        'type','mode','title','keyword',
        'viewtimes','sort','featured','status','createtime','lasttime'
    ];
    const depthStruct  = [
        'gallery'=>DBField_Json,
        'attachments'=>DBField_Json,
        'tags'=>DBField_Json,
        'viewtimes'=>DBField_Int,
        'featured'=>DBField_Boolean,
        'createtime'=>DBField_TimeStamp,
        'lasttime'=>DBField_TimeStamp,
        'sort'=>DBField_Int
    ];

    const tableStruct = [

        'uid'           =>['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'索引ID' , 'idx'=>DBIndex_Unique ],
        'categoryid'    =>['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'分类ID' , 'idx'=>DBIndex_Index ],
        'type'          =>['type'=>DBField_String,    'len'=>16,  'nullable'=>1,  'cmt'=>'类型 text,cover,video,gallery...' ],
        'mode'          =>['type'=>DBField_String,    'len'=>16,  'nullable'=>1,  'cmt'=>'模式' ],
        'saasid'       =>['type'=>DBField_String,  'len'=>8,   'nullable'=>1,                     'cmt'=>'所属saas',  'idx'=>DBIndex_Index,],
        'regionid'      =>['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'地域ID' , 'idx'=>DBIndex_Index ],
        'areaid'        =>['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'地区ID' , 'idx'=>DBIndex_Index ],
        'authorid'      =>['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'创建人ID' ,'idx'=>DBIndex_Index ],

        'title'         =>['type'=>DBField_String,    'len'=>32,  'nullable'=>0,  'cmt'=>'名称名' ,  'idx'=>DBIndex_FullText ],
        'cover'         =>['type'=>DBField_String,    'len'=>255, 'nullable'=>1,  'cmt'=>'封面' ],
        'gallery'       =>['type'=>DBField_Json,      'len'=>-1,  'nullable'=>1,  'cmt'=>'详情介绍' ],
        'attachments'   =>['type'=>DBField_Json,      'len'=>-1,  'nullable'=>1,  'cmt'=>'附件' ],
        'video'         =>['type'=>DBField_String,    'len'=>255, 'nullable'=>1,  'cmt'=>'缩略图' ],
        'link'          =>['type'=>DBField_String,    'len'=>255, 'nullable'=>1,  'cmt'=>'链接' ],
        'tags'          =>['type'=>DBField_Json,      'len'=>255, 'nullable'=>1,  'cmt'=>'标签 最高255' ],

        'description'   =>['type'=>DBField_String,    'len'=>255, 'nullable'=>1,  'cmt'=>'描述' ,   'idx'=>DBIndex_FullText ],
        'introduce'     =>['type'=>DBField_RichText,  'len'=>-1,  'nullable'=>1,  'cmt'=>'详情介绍' ],

        'viewtimes'     =>['type'=>DBField_Int,       'len'=>13,  'nullable'=>0,  'cmt'=>'播放次数',  'dft'=>0,       ],

        'status'        =>['type'=>DBField_String,    'len'=>12,  'nullable'=>0,  'cmt'=>'状态',    'dft'=>'enabled',       ],

        'createtime'    =>['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'创建时间',           'idx'=>DBIndex_Index, ],
        'lasttime'      =>['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'上一次更新时间', ],
        'featured'      =>['type'=>DBField_Boolean,  'len'=>1,  'nullable'=>0,  'cmt'=>'置顶',  'dft'=>0,    'idx'=>DBIndex_Index, ],
        'sort'          =>['type'=>DBField_Int,      'len'=>5,  'nullable'=>0,  'cmt'=>'优先排序','dft'=>0,   'idx'=>DBIndex_Index, ],
    ];

}
