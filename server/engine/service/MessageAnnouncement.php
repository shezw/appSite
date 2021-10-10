<?php

namespace APS;

/**
 * 公告
 */
class MessageAnnouncement extends ASModel{


    const table     = "message_announcement";
    const comment   = '公告';
    const primaryid = "uid";
    const addFields = [
        'uid','saasid', 'authorid',
        'title','content','cover','gallery','attachments','video','link',
        'type', 'status',
        'sort','featured','createtime', 'lasttime',
    ];
    const updateFields = [
        'title','content','cover','gallery','attachments','video','link',
        'type', 'status',
        'sort','featured','createtime', 'lasttime',
    ];   // 更新支持字段
    const detailFields = [
        'uid','saasid', 'authorid',
        'title','content','cover','gallery','attachments','video','link',
        'type', 'status',
        'sort','featured','createtime', 'lasttime',
    ];
    const overviewFields = [
        'uid','saasid', 'authorid',
        'title','content','cover','gallery','attachments','video','link',
        'type', 'status',
        'sort','featured','createtime', 'lasttime',
    ]; // 概览支持字段
    const listFields = [
        'uid','saasid', 'authorid',
        'title','content','cover','gallery','attachments','video','link',
        'type', 'status',
        'sort','featured','createtime', 'lasttime',
    ];     // 列表支持字段
    const filterFields = [
        'uid','saasid',
        'type', 'status',
        'sort','featured','createtime', 'lasttime',
    ];
    const depthStruct = [
        'gallery'=>DBField_Json,
        'attachments'=>DBField_Json,
        'sort'=>DBField_Int,
        'featured'=>DBField_Boolean,
        'createtime'=>DBField_TimeStamp,
        'lasttime'=>DBField_TimeStamp,
    ];

    const tableStruct = [

        'uid'=>      ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'公告ID' , 'idx'=>DBIndex_Unique ],
        'saasid'   =>['type'=>DBField_String,  'len'=>8,   'nullable'=>1,                     'cmt'=>'所属saas',  'idx'=>DBIndex_Index,],
        'authorid'=> ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'作者ID' , 'idx'=>DBIndex_Index ],
        'type'=>     ['type'=>DBField_String,    'len'=>32,  'nullable'=>1,  'cmt'=>'公告类型' ],

        'title'=>    ['type'=>DBField_String,    'len'=>64,  'nullable'=>0,  'cmt'=>'名称' ],
        'content'=>  ['type'=>DBField_RichText,  'len'=>-1,  'nullable'=>1,  'cmt'=>'消息内容' ],
        'cover'=>    ['type'=>DBField_String,    'len'=>255, 'nullable'=>1,  'cmt'=>'封面' ],
        'gallery'=>  ['type'=>DBField_Json,      'len'=>-1,  'nullable'=>1,  'cmt'=>'图册' ],
        'attachments'=>   ['type'=>DBField_Json,      'len'=>-1,  'nullable'=>1,  'cmt'=>'附件' ],
        'video'=>    ['type'=>DBField_String,    'len'=>255, 'nullable'=>1,  'cmt'=>'视频' ],
        'link'=>     ['type'=>DBField_String,    'len'=>255, 'nullable'=>1,  'cmt'=>'链接' ],

        'status'=>   ['type'=>DBField_String,    'len'=>24,  'nullable'=>0,  'cmt'=>'状态',    'dft'=>Status_Enabled,   ],
        // eg: sent 已发送 received 已接收 read 已读

        'createtime'   =>['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'创建时间',                      'idx'=>DBIndex_Index, ],
        'lasttime'     =>['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'上一次更新时间', ],
        'featured'     =>['type'=>DBField_Boolean,  'len'=>1,  'nullable'=>0,  'cmt'=>'置顶',  'dft'=>0,               'idx'=>DBIndex_Index, ],
        'sort'         =>['type'=>DBField_Int,      'len'=>5,  'nullable'=>0,  'cmt'=>'优先排序','dft'=>0,              'idx'=>DBIndex_Index, ],

    ];


}