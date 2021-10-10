<?php

namespace APS;

/**
 * 消息
 * MessageNotification
 * Notification消息主要分3种:
 *      信息 Message      ( 用户相互发送的站内信     Internal messages sent by users to each other)
 *      通知 Notify       ( 系统向用户发送的站内信   Internal message sent by the system to users )
 *      反馈 Suggest      ( 用户向系统发送的意见反馈 User feedback to the system )
 * @package APS\service
 */
class MessageNotification extends ASModel{


    const table     = "message_notification";
    const comment   = '消息-通知';
    const primaryid = "uid";
    const addFields = [
        'uid','saasid', 'senderid', 'receiverid', 'replyid',
        'type', 'status',
        'content', 'link', 'linkparams', 'linktype',
    ];
    const updateFields = [
        'status', 'link', 'replyid', 'linkparams', 'linktype',
    ];
    const detailFields = [
        'uid','saasid', 'senderid', 'receiverid', 'replyid',
        'type', 'status',
        'content', 'link', 'linkparams', 'linktype',
        'sort','featured','createtime', 'lasttime',
    ];
    const overviewFields = [
        'uid','saasid', 'senderid', 'receiverid', 'replyid',
        'type', 'status',
        'content', 'link', 'linkparams', 'linktype',
        'sort','featured','createtime', 'lasttime',
    ];
    const listFields = [
        'uid','saasid', 'senderid', 'receiverid', 'replyid',
        'type', 'status',
        'content', 'link', 'linkparams', 'linktype',
        'sort','featured','createtime', 'lasttime',
    ];
    const filterFields = [
        'uid','saasid', 'senderid', 'receiverid', 'replyid',
        'type', 'status',
        'linktype',
        'createtime',
        'sort','featured','createtime', 'lasttime',
    ];
    const depthStruct = [
        'linkparams'=>DBField_Json,
        'sort'=>DBField_Int,
        'featured'=>DBField_Boolean,
        'createtime'=>DBField_TimeStamp,
        'lasttime'=>DBField_TimeStamp,
    ];

    const tableStruct = [

        'uid'=>         ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'通知ID' , 'idx'=>DBIndex_Unique ],
        'saasid'=>      ['type'=>DBField_String,  'len'=>8,   'nullable'=>1,    'cmt'=>'所属saas',  'idx'=>DBIndex_Index,],
        'senderid'=>    ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'发送方ID' ,        'idx'=>DBIndex_Index ],
        'receiverid'=>  ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'接收方ID' ,        'idx'=>DBIndex_Index ],
        'replyid'=>     ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'回复到ID' ],
        'status'=>      ['type'=>DBField_String,    'len'=>24,  'nullable'=>0,  'cmt'=>'状态',    'dft'=>'sent', ],

        'content'=>     ['type'=>DBField_String,    'len'=>512, 'nullable'=>1,  'cmt'=>'消息内容' ],

        'type'=>        ['type'=>DBField_String,    'len'=>32,  'nullable'=>0,  'cmt'=>'消息类型',  'dft'=>'normal',        ],

        'link'=>        ['type'=>DBField_String,    'len'=>255, 'nullable'=>1,  'cmt'=>'消息链接url' ],
        'linkparams'=>  ['type'=>DBField_Json,      'len'=>511, 'nullable'=>1,  'cmt'=>'消息链接参数 k-v json' ],
        'linktype'=>    ['type'=>DBField_String,    'len'=>16,  'nullable'=>1,  'cmt'=>'消息链接类型' ],

        'createtime'=>  ['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'创建时间',      'idx'=>DBIndex_Index, ],
        'lasttime'=>    ['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'上一次更新时间', ],
    ];


    /**
     * 普通相互发送消息
     * send message to user
     * @param  string  $from    发送方id Sender Userid
     * @param  string  $to      接收方id Receiver Userid
     * @param  string  $content 内容
     * @param  string  $type    ?类型
     * @return ASResult
     */
    public function send( string $from, string $to, string $content , string $type = 'message'  ): ASResult
    {

        return $this->add(static::initValuesFromArray(['status'=>'send','senderid'=>$from,'receiverid'=>$to,'content'=>$content,'type'=>$type]));
    }


    /**
     * 系统给用户发送通知
     * System notify to user
     * @param  string  $userid
     * @param  string  $content
     * @param  string  $link
     * @param  array   $linkParams
     * @param  string  $linkType
     * @return ASResult
     */
    public function notify( string $userid, string $content, string $link = null, array $linkParams = null, string $linkType = null ): ASResult
    {
        $data = static::initValuesFromArray(['status'=>'send','senderid'=>'system','receiverid'=>$userid,'content'=>$content,'type'=>'notify','link'=>$link,'linkparams'=>$linkParams,'linktype'=>$linkType]);
        return $this->add($data);
    }

    /**
     * 用户给系统发送意见反馈
     * Send suggest to system
     * @param  string  $userid
     * @param  string  $content
     * @param  string  $link
     * @param  array   $linkParams
     * @param  string  $linkType
     * @return ASResult
     */
    public function suggest( string $userid, string $content, string $link = null, array $linkParams = null, string $linkType = null): ASResult
    {
        $data = static::initValuesFromArray(['status'=>'send','senderid'=>$userid,'receiverid'=>'system','content'=>$content,'type'=>'suggest','link'=>$link,'linkparams'=>$linkParams,'linktype'=>$linkType]);

        return $this->add($data);
    }


    // **** 收信部分

    /**
     * 所有发信 计数
     * countSent
     * @param string $userid
     * @param string|null $type
     * @param string|null $status
     * @param DBConditions|null $moreFilters
     * @return ASResult
     */
    public function countSent( string $userid , string $type = null , string $status = null, DBConditions $moreFilters = null ): ASResult
    {
        $filters = $moreFilters ?? DBConditions::init(static::table)
                ->where('senderid')->equal($userid)
                ->and('type')->equalIf($type)
                ->and('status')->equalIf($status);

        return $this->count($filters);
    }

    /**
     * 所有收信 计数
     * countReceive
     * @param string $userid
     * @param string|null $type
     * @param string|null $status
     * @param DBConditions|null $moreFilters
     * @return ASResult
     */
    public function countReceive( string $userid, string $type = null, string $status = null, DBConditions $moreFilters = null ): ASResult
    {
        $filters = $moreFilters ?? DBConditions::init(static::table)
                ->where('receiverid')->equal($userid)
                ->and('type')->equalIf($type)
                ->and('status')->equalIf($status);
        return $this->count($filters);
    }

    /**
     * 未读收信条数
     * countNew
     * @param  string  $userid
     * @param  string  $type
     * @return ASResult
     */
    public function countNew( string $userid , string $type = 'message' ): ASResult
    {
        $conditions = DBConditions::init(static::table)
            ->where('receiverid')->equal($userid)
            ->and('type')->equal($type)
            ->and('status')->equalIf('send');
        return $this->count($conditions);
    }

    public function countNewMessage( string $userid ):ASResult { return $this->countNew($userid,'message'); }
    public function countNewNotification( string $userid ):ASResult { return $this->countNew($userid,'notify'); }
    public function countNewSuggest(  ):ASResult { return $this->countNew('system','suggest'); }

    /**
     * 我的消息 计数
     * countMessage
     * @param  string       $userid
     * @param  string|null  $status
     * @param  string|null  $senderid
     * @return ASResult
     */
    public function countMyMessage( string $userid , string $status = null, string $senderid = null ): ASResult
    {
        $conditions = DBConditions::init(static::table)
            ->where('receiverid')->equal($userid)
            ->and('type')->equal('message')
            ->and('status')->equalIf($status)
            ->and('senderid')->equalIf($senderid);
        return $this->count($conditions);
    }

    /**
     * 我的通知 计数
     * countNotification
     * @param  string       $userid
     * @param  string|null  $status
     * @return ASResult
     */
    public function countMyNotification( string $userid , string $status = null ): ASResult
    {
        $conditions = DBConditions::init(static::table)
            ->where('receiverid')->equal($userid)
            ->and('status')->equalIf($status)
            ->and('type')->equal('notify');
        return $this->count($conditions);
    }

    /**
     * 我的已发送 计数
     * countSentMessage
     * @param  string       $userid
     * @param  string|null  $status
     * @param  string|null  $receiverid
     * @return ASResult
     */
    public function countSentMessage( string $userid, string $status = null, string $receiverid = null ): ASResult
    {
        $conditions = DBConditions::init(static::table)
            ->where('senderid')->equal($userid)
            ->and('receiverid')->equalIf($receiverid)
            ->and('status')->equalIf($status)
            ->and('type')->equal('message');
        return $this->count($conditions);
    }

    /**
     * 我的意见反馈 计数
     * countMySuggest
     * @param  string       $userid
     * @param  string|null  $status
     * @return ASResult
     */
    public function countMySuggest( string $userid, string $status = null ): ASResult
    {
        $conditions = DBConditions::init(static::table)
            ->where('senderid')->equal($userid)
            ->and('receiverid')->equal('system')
            ->and('status')->equalIf($status)
            ->and('type')->equal('suggest');
        return $this->count($conditions);
    }

    /**
     * 我的通知列表
     * myNotificationList
     * @param  string       $userid
     * @param  string|null  $status
     * @param  int          $page
     * @param  int          $size
     * @return ASResult
     */
    public function myNotificationList( string $userid, string $status = null, $page = 1, $size = 20 ): ASResult
    {
        $conditions = DBConditions::init(static::table)
            ->where('receiverid')->equal($userid)
            ->and('status')->equalIf($status)
            ->and('type')->equal('notify');
        return $this->list($conditions,$page,$size, 'createtime DESC');
    }

    /**
     * 我的已发送列表
     * my sent Message List
     * @param  string       $userid
     * @param  string|null  $status
     * @param  string|null  $receiverid
     * @param  int          $page
     * @param  int          $size
     * @return ASResult
     */
    public function mySentMessageList( string $userid, string $status = null, string $receiverid = null, int $page = 1, int $size = 20 ): ASResult
    {
        $conditions = DBConditions::init(static::table)
            ->where('senderid')->equal($userid)
            ->and('status')->equalIf($status)
            ->and('receiverid')->equalIf($receiverid)
            ->and('type')->equal('message');
        return $this->list($conditions,$page,$size);
    }

    /**
     * 我的意见反馈列表
     * mySuggestList
     * @param  string       $userid
     * @param  string|null  $status
     * @param  int          $page
     * @param  int          $size
     * @return ASResult
     */
    public function mySuggestList( string $userid, string $status = null, int $page = 1, int $size = 20 ): ASResult
    {
        $conditions = DBConditions::init(static::table)
            ->where('senderid')->equal($userid)
            ->and('status')->equalIf($status)
            ->and('receiverid')->equalIf('system')
            ->and('type')->equal('suggest');
        return $this->list($conditions,$page,$size);
    }


    // 对系统内单位进行标记( 进行认证反馈或是回复标注 )
    public function mark( $content, string $itemid, string $itemtype ): ASResult
    {
        return $this->add(static::initValuesFromArray(['status'=>'send','senderid'=>'system','receiverid'=>$itemid,'type'=>$itemtype,'content'=>$content]));
    }


    /**
     * 查询收信列表
     * myMessageList
     * @param  string|null  $userid
     * @param  string|null  $status
     * @param  string|null  $senderid
     * @param  int          $page
     * @param  int          $size
     * @return ASResult
     */
    public function myMessageList( string $userid = null, string $status = null, string $senderid = null, int $page = 1, int $size = 20 ): ASResult
    {
        $conditions = DBConditions::init(static::table)
            ->where('receiverid')->equal($userid)
            ->and('status')->equalIf($status)
            ->and('senderid')->equalIf($senderid);
        return $this->list($conditions,$page,$size);
    }

    /**
     * 设置已读
     * Set read
     * @param string $uid
     * @return ASResult
     */
    public function read( string $uid ): ASResult
    {
        return $this->status($uid,'read');
    }


    /**
     * 设置已回复
     * Set replied
     * @param string $uid
     * @param string|null $replyId
     * @return ASResult
     */
    public function replied( string $uid , string $replyId = null ): ASResult
    {

        return $this->update(static::initValuesFromArray(['status'=>'replied','replyid'=>$replyId]),$uid);

    }

    /**
     * 批量已读
     * readAll
     * @param  string       $userid
     * @param  string|null  $type
     * @param  string|null  $senderId
     * @return ASResult
     */
    public function readAll( string $userid, string $type = null, string $senderId = null): ASResult
    {
        $conditions = DBConditions::init(static::table)
            ->where('receiverid')->equal($userid)
            ->and('senderid')->equalIf($senderId)
            ->and('type')->equalIf($type);

        return $this->getDB()->update(DBValues::init('status')->string('read'),static::table,$conditions);
    }


    public function suggestList( DBConditions $filters ): ASResult
    {
        $filters->and('type')->equal('suggest');

        return $this->list($filters);

    }

    public function messageList( DBConditions $filters ): ASResult
    {
        $filters->and('type')->equal('message');

        return $this->list($filters);

    }

    public function notificationList( DBConditions $filters ): ASResult
    {
        $filters->and('type')->equal('notify');

        return $this->list($filters);

    }


}