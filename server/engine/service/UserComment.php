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

    const table     = "user_comment";
    const comment   = '用户-评论';
    const primaryid = "uid";
    const addFields = [
        'uid','userid',
        'itemid','itemtype',
        'title','content','status',
        'featured','sort','createtime','lasttime',
    ];
    const updateFields = [
        'featured',
        'status',
    ];
    const detailFields = [
        'uid','userid',
        'itemid','itemtype',
        'title','content','status',
        'featured','sort','createtime','lasttime',
    ];
    const overviewFields = [
        'uid','userid',
        'itemid','itemtype',
        'title','content','status',
        'featured','sort','createtime','lasttime',
    ];
    const listFields = [
        'uid','userid',
        'itemid','itemtype',
        'title','content','status',
        'featured','sort','createtime','lasttime',
    ];
    const filterFields = [
        'uid','userid',
        'itemid','itemtype',
        'featured','sort','createtime','lasttime',
        'status'
    ];
    const depthStruct = [
        'featured'=>DBField_Boolean,
        'sort'=>DBField_Int,
        'createtime'=>DBField_TimeStamp,
        'lasttime'=>DBField_TimeStamp,
    ];

    const tableStruct = [

        'uid'=>      ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'评论ID' , 'idx'=>DBIndex_Unique ],
        'userid'=>   ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'用户ID' , 'idx'=>DBIndex_Index ],
        'itemid'=>   ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'回复ID ' ,'idx'=>DBIndex_Index ],
        'itemtype'=> ['type'=>DBField_String,    'len'=>32,  'nullable'=>1,  'cmt'=>'回复类型' ,'idx'=>DBIndex_Index ],

        'title'=>    ['type'=>DBField_String,    'len'=>63,  'nullable'=>1,  'cmt'=>'标题 30字以内' ,     'idx'=>DBIndex_FullText ],
        'content'=>  ['type'=>DBField_String,    'len'=>511, 'nullable'=>1,  'cmt'=>'内容 250字以内' ,    'idx'=>DBIndex_FullText ],

        'status'=>   ['type'=>DBField_String,    'len'=>12,  'nullable'=>0,  'cmt'=>'状态',    'dft'=>'enabled',       ],

        'createtime'   =>['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'创建时间',                      'idx'=>DBIndex_Index, ],
        'lasttime'     =>['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'上一次更新时间', ],
        'featured'     =>['type'=>DBField_Boolean,  'len'=>1,  'nullable'=>0,  'cmt'=>'置顶',  'dft'=>0,               'idx'=>DBIndex_Index, ],
        'sort'         =>['type'=>DBField_Int,      'len'=>5,  'nullable'=>0,  'cmt'=>'优先排序','dft'=>0,              'idx'=>DBIndex_Index, ],
    ];


    private $userid;

    public function __construct( string $userid = NULL )
    {
        parent::__construct();

        $this->userid = $userid;
    }


/** 通用查询部分 */

    /**
     * 查询评论列表 根据itemid,itemtype查询评论
     * commentList by itemid,itemtype
     * @param string $itemid
     * @param string $itemType
     * @param int $page
     * @param int $size
     * @param string|NULL $sort
     * @param DBConditions|NULL $moreFilters
     * @return ASResult
     */
    public function commentList( string $itemid , string $itemType, int $page=1, int $size=25 , string $sort = NULL, DBConditions $moreFilters = NULL ): ASResult
    {
        $filters = $moreFilters ?? DBConditions::init(static::table);
        $filters->and('itemid')->equal($itemid)
                ->and('itemtype')->equal($itemType);

        if ( $this->countComment($itemid,$itemType,$filters)>0 ) {
            return $this->list($filters,$page,$size,$sort ?? 'featured DESC, createtime DESC');
        }else{
            return $this->take($itemid)->error(400,i18n('SYS_NON'),'UserComment->replyList');
        }
    }

    /**
     * 评论计数
     * countComment
     * @param string $itemid
     * @param string $itemType
     * @param DBConditions|null $moreFilters
     * @return ASResult
     */
    public function countComment( string $itemid , string $itemType, DBConditions $moreFilters = NULL ): ASResult
    {
        $filters = $moreFilters ?? DBConditions::init(static::table);
        $filters->and('itemid')->equal($itemid)
            ->and('itemtype')->equal($itemType);

        return $this->count($filters);
    }


    /**
     * 是否被评论
     * isCommented
     * @param string $itemid
     * @param string $itemType
     * @param DBConditions|null $moreFilters
     * @return bool
     */
    public function isCommented( string $itemid , string $itemType, DBConditions $moreFilters = NULL ): bool
    {
        return $this->countComment( $itemid,$itemType,$moreFilters )->getContent() > 0;
    }


    /**
     * 查询回复列表 根据comment查询回复内容
     * replyList by uid
     * @param string $uid
     * @param int $page
     * @param int $size
     * @param NULL $sort
     * @param DBConditions|null $moreFilters
     * @return ASResult
     */
    public function replyList( string $uid , $page=1, $size=25 , $sort = NULL, DBConditions $moreFilters = NULL ): ASResult
    {
        $filters = $moreFilters ?? DBConditions::init(static::table);
        $filters->and('itemid')->equal($uid)
            ->and('itemtype')->equal(static::table);

        if ( $this->count($filters)>0 ) {
            return $this->list($filters,$page,$size,$sort??'featured DESC, createtime DESC');
        }else{
            return $this->take($uid)->error(400,i18n('SYS_NON'),'UserComment->replyList');
        }
    }

    /**
     * 回复计数
     * countReply
     * @param string $uid
     * @param DBConditions|NULL $moreFilters
     * @return ASResult
     */
    public function countReply( string $uid, DBConditions $moreFilters = NULL ): ASResult
    {
        $filters = $moreFilters ?? DBConditions::init(static::table);
        $filters->and('itemid')->equal($uid)
            ->and('itemtype')->equal(static::table);

        return $this->count($filters);
    }


    /**
     * 是否被回复
     * isReplied
     * @param string $uid
     * @param DBConditions|null $moreFilters
     * @return bool
     */
    public function isReplied( string $uid , DBConditions $moreFilters = NULL ): bool
    {
        return $this->countReply( $uid,$moreFilters )->getContent() > 0;
    }


/** 用户评论相关 */

    /**
     * 我的评论列表
     * myCommentList
     * @param string $itemid
     * @param string $itemType
     * @param int    $page
     * @param int    $size
     * @param NULL   $sort
     * @param DBConditions|null $moreFilters
     * @return ASResult
     */
    public function myCommentList( string $itemid , string $itemType, $page=1, $size=25 , $sort = NULL, DBConditions $moreFilters = NULL ): ASResult
    {
        if( isset($this->userid) ){ return $this->error(150,'Need Userid instance','UserComment->myCommentList'); }

        $filters = $moreFilters ?? DBConditions::init(static::table);
        $filters->and('userid')->equal($this->userid);

        return $this->commentList( $itemid,$itemType,$page,$size,$sort, $filters );
    }

    /**
     * 我的评论计数
     * countMyComment
     * @param  string      $itemid
     * @param  string      $itemType
     * @param  array|NULL  $moreFilters
     * @return ASResult
     */
    public function countMyComment( string $itemid , string $itemType, array $moreFilters = NULL ): ASResult
    {
        if( isset($this->userid) ){ return $this->error(150,'Need Userid instance','UserComment->myCommentList'); }

        $filters = $moreFilters ?? DBConditions::init(static::table);
        $filters->and('userid')->equal($this->userid);

        return $this->countMyComment( $itemid,$itemType,$filters );
    }

    /**
     * 当前用户是否评论了
     * hasCommentedTo
     * @param  string      $itemid
     * @param  string      $itemType
     * @param  array|NULL  $moreFilters
     * @return ASResult|bool
     */
    public function hasCommentedTo( string $itemid , string $itemType, array $moreFilters = NULL )
    {
        if( isset($this->userid) ){ return $this->error(150,'Need Userid instance','UserComment->myCommentList'); }

        $filters = $moreFilters ?? DBConditions::init(static::table);
        $filters->and('userid')->equal($this->userid);

        return $this->countComment( $itemid,$itemType,$filters )->getContent() > 0;
    }

    /**
     * 我的回复列表
     * myReplyList
     * @param string $uid
     * @param int    $page
     * @param int    $size
     * @param NULL   $sort
     * @param DBConditions|null $moreFilters
     * @return ASResult
     */
    public function myReplyList( string $uid , $page = 1, $size = 25 , $sort = NULL, DBConditions $moreFilters = NULL ): ASResult
    {
        if( isset($this->userid) ){ return $this->error(150,'Need Userid instance','UserComment->myCommentList'); }

        $filters = $moreFilters ?? DBConditions::init(static::table);
        $filters->and('userid')->equal($this->userid);

        return $this->replyList( $uid,$page,$size,$sort, $filters );
    }

    /**
     * 我的回复 计数
     * countMyReply
     * @param string $uid
     * @param DBConditions|NULL $moreFilters
     * @return ASResult
     */
    public function countMyReply( string $uid, DBConditions $moreFilters = NULL ): ASResult
    {
        if( isset($this->userid) ){ return $this->error(150,'Need Userid instance','UserComment->myCommentList'); }

        $filters = $moreFilters ?? DBConditions::init(static::table);
        $filters->and('userid')->equal($this->userid);

        return $this->countReply($uid,$filters);
    }

    /**
     * 是否回复某评论
     * hasRepliedTo specific comment
     * @param string $uid
     * @return ASResult|bool
     */
    public function hasRepliedTo( string $uid )
    {
        if( isset($this->userid) ){ return $this->error(150,'Need Userid instance','UserComment->myCommentList'); }

        $filters = DBConditions::init(static::table)->and('userid')->equal($this->userid);

        return $this->countReply( $uid,$filters )->getContent() > 0;
    }


}