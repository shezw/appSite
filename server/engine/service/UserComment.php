<?php

namespace APS;

/**
 * 评论
 * UserComment
 * 评论部分分为两类:
 * 1.评论: 目标是任意对象,通过itemid,itemType区分.
 * 2.回复: 目标是评论,通过itemid区分 itemType固定为user_comment.
 * The comment section is divided into two categories:
 * 1. Comment: The target is an arbitrary object, distinguished by itemid and itemType.
 * 2. Reply: The target is a comment, distinguished by itemid, itemType is fixed to user_comment.
 *
 * @package APS\service\User
 */
class UserComment extends ASModel{

    private $userid;

    public function __construct( string $userid = null )
    {
        parent::__construct();

        $this->userid = $userid;
    }


/** 通用查询部分 */

    /**
     * 查询评论列表 根据itemid,itemtype查询评论
     * commentList by itemid,itemtype
     * @param  string      $itemid
     * @param  string      $itemType
     * @param  int         $page
     * @param  int         $size
     * @param  null        $sort
     * @param  array|null  $moreFilters
     *                                 userid
     *                                 status
     *                                 featured
     *                                 createtime
     *                                 lasttime
     * @return ASResult
     */
    public function commentList( string $itemid , string $itemType, $page=1, $size=25 , $sort = null, array $moreFilters = null ): ASResult
    {

        $filter = $moreFilters ?? [];
        $filter['itemid'] = $itemid;
        $filter['itemtype'] = $itemType;

        if ( $this->countComment($itemid,$itemType,$moreFilters)>0 ) {
            return $this->list($filter,$page,$size,$sort??'featured DESC, createtime DESC');
        }else{
            return $this->take($itemid)->error(400,i18n('SYS_NON'),'UserComment->replyList');
        }
    }

    /**
     * 评论计数
     * countComment
     * @param  string      $itemid
     * @param  string      $itemType
     * @param  array|null  $moreFilters
     *                                 userid
     *                                 status
     *                                 createtime
     *                                 lasttime
     * @return ASResult
     */
    public function countComment( string $itemid , string $itemType, array $moreFilters = null ): ASResult
    {

        $filter = $moreFilters ?? [];
        $filter['itemid'] = $itemid;
        $filter['itemtype'] = $itemType;

        return $this->count($filter);
    }


    /**
     * 是否被评论
     * isCommented
     * @param  string      $itemid
     * @param  string      $itemType
     * @param  array|null  $moreFilters
     * @return bool
     */
    public function isCommented( string $itemid , string $itemType, array $moreFilters = null ): bool
    {

        return $this->countComment( $itemid,$itemType,$moreFilters )->getContent() > 0;
    }





    /**
     * 查询回复列表 根据comment查询回复内容
     * replyList by uid
     * @param  string      $uid
     * @param  int         $page
     * @param  int         $size
     * @param  null        $sort
     * @param  array|null  $moreFilters
     *                                 userid
     *                                 status
     *                                 featured
     *                                 createtime
     *                                 lasttime
     * @return ASResult
     */
    public function replyList( string $uid , $page=1, $size=25 , $sort = null, array $moreFilters = null ): ASResult
    {

        $filter = $moreFilters ?? [];
        $filter['itemid'] = $uid;
        $filter['itemtype'] = static::$table;

        if ( $this->count($filter)>0 ) {
            return $this->list($filter,$page,$size,$sort??'featured DESC, createtime DESC');
        }else{
            return $this->take($uid)->error(400,i18n('SYS_NON'),'UserComment->replyList');
        }

    }

    /**
     * 回复计数
     * countReply
     * @param  string      $uid
     * @param  array|null  $moreFilters
     *                                 userid
     *                                 status
     *                                 featured
     *                                 createtime
     *                                 lasttime
     * @return ASResult
     */
    public function countReply( string $uid, array $moreFilters = null ): ASResult
    {

        $filter = $moreFilters ?? [];
        $filter['itemid'] = $uid;
        $filter['itemtype'] = static::$table;

        return $this->count($filter);
    }


    /**
     * 是否被回复
     * isReplied
     * @param  string      $uid
     * @param  array|null  $moreFilters
     * @return bool
     */
    public function isReplied( string $uid , array $moreFilters = null ): bool
    {

        return $this->countReply( $uid,$moreFilters )->getContent() > 0;
    }


/** 用户评论相关 */

    /**
     * 我的评论列表
     * myCommentList
     * @param  string      $itemid
     * @param  string      $itemType
     * @param  int         $page
     * @param  int         $size
     * @param  null        $sort
     * @param  array|null  $moreFilters
     * @return ASResult
     */
    public function myCommentList( string $itemid , string $itemType, $page=1, $size=25 , $sort = null, array $moreFilters = null ): ASResult
    {

        if( isset($this->userid) ){ return $this->error(150,'Need Userid instance','UserComment->myCommentList'); }

        $moreFilters = $moreFilters ?? [];
        $moreFilters['userid'] = $this->userid;

        return $this->commentList( $itemid,$itemType,$page,$size,$sort, $moreFilters );
    }

    /**
     * 我的评论计数
     * countMyComment
     * @param  string      $itemid
     * @param  string      $itemType
     * @param  array|null  $moreFilters
     * @return ASResult
     */
    public function countMyComment( string $itemid , string $itemType, array $moreFilters = null ): ASResult
    {

        if( isset($this->userid) ){ return $this->error(150,'Need Userid instance','UserComment->myCommentList'); }

        $moreFilters = $moreFilters ?? [];
        $moreFilters['userid'] = $this->userid;

        return $this->countMyComment( $itemid,$itemType,$moreFilters );
    }

    /**
     * 当前用户是否评论了
     * hasCommenedTo
     * @param  string      $itemid
     * @param  string      $itemType
     * @param  array|null  $moreFilters
     * @return ASResult|bool
     */
    public function hasCommenedTo( string $itemid , string $itemType, array $moreFilters = null ){

        if( isset($this->userid) ){ return $this->error(150,'Need Userid instance','UserComment->myCommentList'); }

        $moreFilters = $moreFilters ?? [];
        $moreFilters['userid'] = $this->userid;

        return $this->countComment( $itemid,$itemType,$moreFilters )->getContent() > 0;

    }

    /**
     * 我的回复列表
     * myReplyList
     * @param  string      $uid
     * @param  int         $page
     * @param  int         $size
     * @param  null        $sort
     * @param  array|null  $moreFilters
     * @return ASResult
     */
    public function myReplyList( string $uid , $page=1, $size=25 , $sort = null, array $moreFilters = null ): ASResult
    {

        if( isset($this->userid) ){ return $this->error(150,'Need Userid instance','UserComment->myCommentList'); }

        $moreFilters = $moreFilters ?? [];
        $moreFilters['userid'] = $this->userid;

        return $this->replyList( $uid,$page,$size,$sort, $moreFilters );
    }

    /**
     * 我的回复 计数
     * countMyReply
     * @param  string      $uid
     * @param  array|null  $moreFilters
     * @return ASResult
     */
    public function countMyReply( string $uid, array $moreFilters = null ): ASResult
    {

        if( isset($this->userid) ){ return $this->error(150,'Need Userid instance','UserComment->myCommentList'); }

        $moreFilters = $moreFilters ?? [];
        $moreFilters['userid'] = $this->userid;

        return $this->countReply($uid,$moreFilters);
    }

    /**
     * 是否回复某评论
     * hasRepliedTo specific comment
     * @param  string  $uid
     * @param  array   $moreFilters
     * @return ASResult|bool
     */
    public function hasRepliedTo( string $uid, array $moreFilters){

        if( isset($this->userid) ){ return $this->error(150,'Need Userid instance','UserComment->myCommentList'); }

        $moreFilters = $moreFilters ?? [];
        $moreFilters['userid'] = $this->userid;

        return $this->countReply( $uid,$moreFilters )->getContent() > 0;

    }


    public static $table     = "user_comment";  // 表
    public static $primaryid = "uid";     // 主字段
    public static $addFields = [
        'title',
        'userid',
        'uid',
        'content',
        'itemid',
        'featured',
        'itemtype',
    ];      // 添加支持字段
    public static $updateFields = [
        'featured',
        'status',
    ];   // 更新支持字段
    public static $detailFields = "*";   // 详情支持字段
    public static $overviewFields = [
        'title',
        'userid',
        'uid',
        'content',
        'itemid',
        'featured',
        'itemtype',
        'createtime',
        'lasttime',
    ]; // 概览支持字段
    public static $listFields = [
        'title',
        'userid',
        'uid',
        'content',
        'itemid',
        'featured',
        'itemtype',
        'createtime',
        'lasttime',
    ];     // 列表支持字段
    public static $countFilters = [
        'userid',
        'uid',
        'itemid',
        'itemtype',
        'featured',
        'createtime',
        'lasttime',
    ];
    public static $depthStruct = [
        'featured'=>'int',
        'createtime'=>'int',
        'lasttime'=>'int',
    ];


}