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


    /**
     * 普通相互发送消息
     * send message to user
     * @param  string  $from    发送方id Sender Userid
     * @param  string  $to      接收方id Receiver Userid
     * @param  string  $content 内容
     * @param  string  $type    ?类型
     * @return \APS\ASResult
     */
    public function send( string $from, string $to, string $content , string $type = 'message'  ){

        return $this->add(['status'=>'send','senderid'=>$from,'receiverid'=>$to,'content'=>$content,'type'=>$type]);
    }


    /**
     * 系统给用户发送通知
     * System notify to user
     * @param  string  $userid
     * @param  string  $content
     * @param  string  $link
     * @param  array   $linkparams
     * @param  string  $linktype
     * @return \APS\ASResult
     */
    public function notify( string $userid, string $content, string $link = null, array $linkparams = null, string $linktype = null ){

        return $this->add(['status'=>'send','senderid'=>'system','receiverid'=>$userid,'content'=>$content,'type'=>'notify','link'=>$link,'linkparams'=>$linkparams,'linktype'=>$linktype]);
    }

    /**
     * 用户给系统发送意见反馈
     * Send suggest to system
     * @param  string  $userid
     * @param  string  $content
     * @param  string  $link
     * @param  array   $linkparams
     * @param  string  $linktype
     * @return \APS\ASResult
     */
    public function suggest( string $userid, string $content, string $link = null, array $linkparams = null, string $linktype = null){

        return $this->add(['status'=>'send','senderid'=>$userid,'receiverid'=>'system','content'=>$content,'type'=>'suggest','link'=>$link,'linkparams'=>$linkparams,'linktype'=>$linktype]);
    }


    // **** 收信部分

    /**
     * 所有发信 计数
     * countSent
     * @param  string       $userid
     * @param  string|null  $type
     * @param  string|null  $status
     * @param  array|null   $moreFilters
     * @return \APS\ASResult
     */
    public function countSent( string $userid , string $type = null , string $status = null, array $moreFilters = null ){

        $moreFilters = array_merge($moreFilters ?? [],['senderid'=>$userid,'type'=>$type,'status'=>$status]);

        return $this->count($moreFilters);
    }

    /**
     * 所有收信 计数
     * countReceive
     * @param  string       $userid
     * @param  string|null  $type
     * @param  string|null  $status
     * @param  array|null   $moreFilters
     * @return \APS\ASResult
     */
    public function countReceive( string $userid, string $type = null, string $status = null, array $moreFilters = null ){

        $moreFilters = array_merge($moreFilters ?? [],['receiverid'=>$userid,'type'=>$type,'status'=>$status]);

        return $this->count($moreFilters);
    }

    /**
     * 未读收信条数
     * countNew
     * @param  string  $userid
     * @param  string  $type
     * @return \APS\ASResult
     */
    public function countNew( string $userid , string $type = 'message' ){

        return $this->count(['receiverid'=>$userid,'status'=>'send','type'=>$type]);
    }

    public function countNewMessage( string $userid ){ return $this->countNew($userid,'message'); }
    public function countNewNotification( string $userid ){ return $this->countNew($userid,'notify'); }
    public function countNewSuggest(  ){ return $this->countNew('system','suggest'); }

    /**
     * 我的消息 计数
     * countMessage
     * @param  string       $userid
     * @param  string|null  $status
     * @param  string|null  $senderid
     * @return \APS\ASResult
     */
    public function countMyMessage( string $userid , string $status = null, string $senderid = null ){
        return $this->count(['receiverid'=>$userid,'senderid'=>$senderid,'status'=>$status,'type'=>'message']);
    }

    /**
     * 我的通知 计数
     * countNotification
     * @param  string       $userid
     * @param  string|null  $status
     * @return \APS\ASResult
     */
    public function countMyNotification( string $userid , string $status = null ){
        return $this->count(['receiverid'=>$userid,'status'=>$status,'type'=>'notify']);
    }

    /**
     * 我的已发送 计数
     * countSentMessage
     * @param  string       $userid
     * @param  string|null  $status
     * @param  string|null  $receiverid
     * @return \APS\ASResult
     */
    public function countSentMessage( string $userid, string $status = null, string $receiverid = null ){

        return $this->count(['senderid'=>$userid,'receiverid'=>$receiverid,'status'=>$status,'type'=>'message']);
    }

    /**
     * 我的意见反馈 计数
     * countMySuggest
     * @param  string       $userid
     * @param  string|null  $status
     * @return \APS\ASResult
     */
    public function countMySuggest( string $userid, string $status = null ){
        return $this->count(['senderid'=>$userid,'receiverid'=>'system','status'=>$status,'type'=>'suggest']);
    }

    /**
     * 我的通知列表
     * myNotificationList
     * @param  string       $userid
     * @param  string|null  $status
     * @param  int          $page
     * @param  int          $size
     * @return \APS\ASResult
     */
    public function myNotificationList( string $userid, string $status = null, $page = 1, $size = 20 ){
        return $this->list(['receiverid'=>$userid,'status'=>$status,'type'=>'notify'],$page,$size, 'createtime DESC');
    }

    /**
     * 我的已发送列表
     * my sent Message List
     * @param  string       $userid
     * @param  string|null  $status
     * @param  string|null  $receiverid
     * @param  int          $page
     * @param  int          $size
     * @return \APS\ASResult
     */
    public function mySentMessageList( string $userid, string $status = null, string $receiverid = null, int $page = 1, int $size = 20 ){
        return $this->list(['senderid'=>$userid,'status'=>$status,'receiverid'=>$receiverid,'type'=>'message'],$page,$size);
    }

    /**
     * 我的意见反馈列表
     * mySuggestList
     * @param  string       $userid
     * @param  string|null  $status
     * @param  int          $page
     * @param  int          $size
     * @return \APS\ASResult
     */
    public function mySuggestList( string $userid, string $status = null, int $page = 1, int $size = 20 ){
        return $this->list(['senderid'=>$userid,'status'=>$status,'receiverid'=>'system','type'=>'suggest'],$page,$size);
    }


    // 对系统内单位进行标记( 进行认证反馈或是回复标注 )
    public function mark( $content, string $itemid, string $itemtype ){

        return $this->add(['status'=>'send','senderid'=>'system','receiverid'=>$itemid,'type'=>$itemtype,'content'=>$content]);
    }


    /**
     * 查询收信列表
     * myMessageList
     * @param  string|null  $userid
     * @param  string|null  $status
     * @param  string|null  $senderid
     * @param  int          $page
     * @param  int          $size
     * @return \APS\ASResult
     */
    public function myMessageList( string $userid = null, string $status = null, string $senderid = null, int $page = 1, int $size = 20 ){

        return $this->list(['receiverid'=>$userid,'status'=>$status,'senderid'=>$senderid],$page,$size);
    }

    // 设置已读
    public function read( string $notificationid ){

        return $this->status($notificationid,'read');

    }

    // 设置已回复
    public function replied( string $notificationid , string $replyid = null ){

        return $this->update(['status'=>'replied','replyid'=>$replyid],$notificationid);

    }

    /**
     * 批量已读
     * readAll
     * @param  string       $userid
     * @param  string|null  $type
     * @param  string|null  $senderid
     * @return \APS\ASResult
     */
    public function readAll( string $userid, string $type = null, string $senderid = null){

        $conditions = [
            'receiverid'=>$userid,
            'senderid'=>$senderid,
            'type'=>$type
        ];

        return $this->getDB()->update(['status'=>'read'],static::$table,$conditions);
    }


    public function suggestList( array $filters ){

        $filters['type'] = 'suggest';

        return $this->list($filters);

    }

    public function messageList( array $filters ){

        $filters['type'] = 'message';

        return $this->list($filters);

    }

    public function notificationList( array $filters ){

        $filters['type'] = 'notify';

        return $this->list($filters);

    }



    public static $table     = "message_notification";
    public static $primaryid = "notificationid";
    public static $addFields = [
        'notificationid', 'senderid', 'receiverid', 'replyid',
        'type', 'status',
        'content', 'link', 'linkparams', 'linktype',
    ];
    public static $updateFields = [
        'status', 'link', 'replyid', 'linkparams', 'linktype',
    ];   // 更新支持字段
    public static $detailFields = "*";
    public static $overviewFields = [
        'notificationid', 'senderid', 'receiverid', 'replyid',
        'type', 'status',
        'content', 'link', 'linkparams', 'linktype',
        'sort','featured','createtime', 'lasttime',
    ]; // 概览支持字段
    public static $listFields = [
        'notificationid', 'senderid', 'receiverid', 'replyid',
        'type', 'status',
        'content', 'link', 'linkparams', 'linktype',
        'sort','featured','createtime', 'lasttime',
    ];     // 列表支持字段
    public static $countFilters = [
        'notificationid', 'senderid', 'receiverid', 'replyid',
        'type', 'status',
        'linktype',
        'createtime',
        'sort','featured','createtime', 'lasttime',
    ];
    public static $depthStruct = [
        'linkparams'=>'ASJson',
        'sort'=>'int',
        'featured'=>'int',
        'createtime'=>'int',
        'lasttime'=>'int',
    ];


}