<?php

namespace APS;

/**
 * 聊天
 * Chat
 */
class MessageChat extends ASModel{


    const table     = "message_chat";
    const comment   = '聊天';
    const primaryid = "uid";
    const addFields = [
        'uid','saasid', 'authorid',
        'title','content','cover','attachments','video','link',
        'type', 'status',
        'sort','featured','createtime', 'lasttime',
    ];
    const updateFields = [
        'title','content','cover','attachments','video','link',
        'type', 'status',
        'sort','featured','createtime', 'lasttime',
    ];
    const detailFields = [
        'uid','saasid', 'authorid',
        'title','content','cover','attachments','video','link',
        'type', 'status',
        'sort','featured','createtime', 'lasttime',
    ];
    const overviewFields = [
        'uid','saasid', 'authorid',
        'title','content','cover','attachments','video','link',
        'type', 'status',
        'sort','featured','createtime', 'lasttime',
    ];
    const listFields = [
        'uid','saasid', 'authorid',
        'title','content','cover','attachments','video','link',
        'type', 'status',
        'sort','featured','createtime', 'lasttime',
    ];
    const filterFields = [
        'uid','saasid',
        'type', 'status',
        'sort','featured','createtime', 'lasttime',
    ];
    const depthStruct = [
        'attachments'=>DBField_Json,
        'sort'=>DBField_Int,
        'featured'=>DBField_Boolean,
        'createtime'=>DBField_TimeStamp,
        'lasttime'=>DBField_TimeStamp,
    ];

    const tableStruct = [

        'uid'=>         ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,      'cmt'=>'消息ID' ,  'idx'=>DBIndex_Unique ],
        'saasid'=>      ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,      'cmt'=>'所属saas',  'idx'=>DBIndex_Index,],
        'senderid'=>    ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,      'cmt'=>'发送方ID' ,        'idx'=>DBIndex_Index ],
        'receiveid'=>   ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,      'cmt'=>'接收放ID' ,        'idx'=>DBIndex_Index ],
        'atuserid'=>    ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,      'cmt'=>'提示到用户ID' ],
        'status'=>      ['type'=>DBField_String,    'len'=>24,  'nullable'=>0,      'cmt'=>'状态' ,   'dft'=>'sent', ],

        'content'=>     ['type'=>DBField_String,    'len'=>512, 'nullable'=>1,      'cmt'=>'消息内容' ],

        'type'=>        ['type'=>DBField_String,    'len'=>32,  'nullable'=>0,      'cmt'=>'消息类型',  'dft'=>'normal',  ],

        'link'=>        ['type'=>DBField_String,    'len'=>255, 'nullable'=>1,      'cmt'=>'消息链接url' ],
        'linkparams'=>  ['type'=>DBField_Json,      'len'=>511, 'nullable'=>1,      'cmt'=>'消息链接参数 k-v json' ],
        'linktype'=>    ['type'=>DBField_String,    'len'=>16,  'nullable'=>1,      'cmt'=>'消息链接类型' ],

        'createtime'=>  ['type'=>DBField_TimeStamp, 'len'=>13, 'nullable'=>0,  'cmt'=>'创建时间',         'idx'=>DBIndex_Index, ],
        'lasttime'=>    ['type'=>DBField_TimeStamp, 'len'=>13, 'nullable'=>0,  'cmt'=>'上一次更新时间', ],
        'featured'=>    ['type'=>DBField_Boolean,   'len'=>1,  'nullable'=>0,  'cmt'=>'置顶',  'dft'=>0,  'idx'=>DBIndex_Index, ],
    ];


}