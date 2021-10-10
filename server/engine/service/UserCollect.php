<?php

namespace APS;

/**
 * 用户收藏
 * UserCollect
 *
 * 收藏组件提供 收藏、点赞、关注、分享记录功能 其区分方式基于type字段
 * The User Collection component provides Favorite, like, and follow functions. The difference is based on the type field.
 *
 * @package APS\service\User
 */
class UserCollect extends ASModel{

    const table     = "user_collect";
    const comment   = '用户-收藏';
    const addFields = [
        'uid', 'userid', 'type', 'itemid', 'itemtype',
        'title', 'cover', 'description',
        'contents',
        'rate', 'status','featured','sort'
    ];
    const updateFields = [
        'rate',
        'status',
    ];
    const detailFields = [
        'uid', 'userid', 'type', 'itemid', 'itemtype',
        'title', 'cover', 'description',
        'contents',
        'rate', 'status', 'createtime', 'lasttime','featured','sort'
    ];
    const overviewFields = [
        'uid', 'userid', 'type', 'itemid', 'itemtype',
        'title', 'cover', 'description',
        'contents',
        'rate', 'status', 'createtime', 'lasttime','featured','sort'
    ];
    const listFields = [
        'uid', 'userid', 'type', 'itemid', 'itemtype',
        'title', 'cover', 'description',
        'rate', 'status', 'createtime', 'lasttime','featured','sort'
    ];
    const filterFields = [
        'uid', 'userid', 'type', 'itemid', 'itemtype',
        'rate', 'status', 'createtime', 'lasttime','featured','sort'
    ];
    const depthStruct = [
        'rate'=>DBField_Int,
        'createtime'=>DBField_TimeStamp,
        'lasttime'=>DBField_TimeStamp,
        'contents'=>DBField_Json
    ];

    const tableStruct = [

        'uid'=>         ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'主ID',   'idx'=>DBIndex_Unique ],
        'userid'=>      ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'用户ID',  'idx'=>DBIndex_Index ],
        'type'=>        ['type'=>DBField_String,    'len'=>16,  'nullable'=>1,  'cmt'=>'类型 ',   'idx'=>DBIndex_Index ],
        'itemid'=>      ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'回复ID ', 'idx'=>DBIndex_Index ],
        'itemtype'=>    ['type'=>DBField_String,    'len'=>32,  'nullable'=>1,  'cmt'=>'回复类型',  'idx'=>DBIndex_Index ],
        'rate'=>        ['type'=>DBField_Int,       'len'=>3,   'nullable'=>0,  'cmt'=>'强度',    'dft'=>1,       ],
        // like type eg: superlike 5  like 1  normal 0 dislike -1  hate -5
        'title'=>       ['type'=>DBField_String,    'len'=>63,  'nullable'=>1,  'cmt'=>'标题',    'idx'=>DBIndex_FullText ],
        'cover'=>       ['type'=>DBField_String,    'len'=>255, 'nullable'=>1,  'cmt'=>'封面'],
        'description'=> ['type'=>DBField_String,    'len'=>255, 'nullable'=>1,  'cmt'=>'描述',    'idx'=>DBIndex_FullText ],
        'contents'=>    ['type'=>DBField_Json,  'len'=>-1,  'nullable'=>1,  'cmt'=>'内容'],
        'status'=>      ['type'=>DBField_String,    'len'=>12,  'nullable'=>0,  'cmt'=>'状态',    'dft'=>Status_Enabled, ],

        'createtime'=>  ['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'创建时间',           'idx'=>DBIndex_Index, ],
        'lasttime'=>    ['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'上一次更新时间', ],
        'featured'=>    ['type'=>DBField_Boolean,  'len'=>1,  'nullable'=>0,  'cmt'=>'置顶',  'dft'=>0,    'idx'=>DBIndex_Index, ],
        'sort'=>        ['type'=>DBField_Int,      'len'=>5,  'nullable'=>0,  'cmt'=>'优先排序','dft'=>0,   'idx'=>DBIndex_Index, ],
    ];
    /**
     * 收集
     * collect
     * @param  string       $userid
     * @param  string       $itemid
     * @param  string|null  $itemType
     * @param  string       $type
     * @param  int|null     $rate
     * @param  array|null   $extraInformation  更多补充信息
     *                                         标题 title
     *                                         描述 description
     *                                         封面 cover
     *                                         更多 contents
     * @return ASResult
     */
    public function collect( string $userid, string $itemid, string $itemType = null, string $type = 'like', int $rate = null, array $extraInformation = null ): ASResult
    {

        $checkCollect = $this->getCollectId( $userid,$itemid,$itemType,$type );
        $extraInformation = isset($extraInformation) ? Filter::purify( $extraInformation, static::addFields ) : [];

        $data = [
            'type'=>$type,
            'userid'=>$userid,
            'itemid'=>$itemid,
            'itemtype'=>$itemType,
            'rate'=>$rate
        ];
        $data = array_merge($data,$extraInformation);

        if( $checkCollect->isSucceed() ){
            return $this->updateByArray($data, $checkCollect->getContent() );
        }else{
            return $this->addByArray($data);
        }
    }

    /**
     * 是否收藏状态
     * hasCollected
     * @param  string       $userid
     * @param  string       $itemid
     * @param  string|null  $itemType
     * @param  string       $type
     * @param  int|null     $rate
     * @return bool
     */
    public function hasCollected( string $userid, string $itemid, string $itemType = null, string $type='like', int $rate = null): bool
    {
        $conditions = DBConditions::init(static::table)
            ->where('type')->equal($type)
            ->and('userid')->equal($userid)
            ->and('itemid')->equal($itemid)
            ->and('itemtype')->equalIf($itemType)
            ->and('rate')->equalIf($rate);

        return $this->getDB()->count(static::table,$conditions)->getContent() > 0;
    }

    /**
     * 取消收藏
     * cancel
     * @param  string       $userid
     * @param  string       $itemid
     * @param  string|null  $itemType
     * @param  string       $type
     * @return ASResult
     */
    public function cancelWith( string $userid, string $itemid, string $itemType = null , string $type='like' ): ASResult
    {

        $checkCollect = $this->getCollectId( $userid,$itemid,$itemType,$type );

        if( $checkCollect->isSucceed() ) {
            return $this->remove( $checkCollect->getContent() );
        }else{
            return $this->success();
        }
    }

    /**
     * 查询收藏id
     * getCollectId
     * @param  string       $userid
     * @param  string       $itemid
     * @param  string|null  $itemType
     * @param  string       $type
     * @return ASResult
     */
    public function getCollectId( string $userid, string $itemid, string $itemType = null, string $type='like' ): ASResult
    {
        $conditions = DBConditions::init(static::table)
            ->where('type')->equal($type)
            ->and('userid')->equal($userid)
            ->and('itemid')->equal($itemid)
            ->and('itemtype')->equalIf($itemType);

        $getCollectList = $this->list($conditions,1,1,'createtime DESC');

        if( !$getCollectList->isSucceed() ){
            return $this->error(400,i18n('SYS_GET_FAL'));
        }
        return $this->take($getCollectList->getContent()[0]['uid'])->success();
    }

/** Favorite 收藏功能 */

    /**
     * 添加收藏
     * favorite
     * @param  string       $userid
     * @param  string       $itemid
     * @param  string|null  $itemType
     * @param  array|null   $params            更多
     *                      标题 title
     *                      描述 description
     *                      封面 cover
     *                      更多 contents
     * @return ASResult
     */
    public function favorite( string $userid, string $itemid, string $itemType = null, array $params = null ): ASResult
    {
        return $this->collect( $userid,$itemid,$itemType,'favorite', null, $params );
    }

    # 取消收藏
    public function unFavorite( string $userid, string $itemid, string $itemType = null ): ASResult
    {
        return $this->cancelWith($userid,$itemid,$itemType,'favorite');
    }

    # 是否收藏
    public function hasFavorited( string $userid, string $itemid, string $itemType ): bool
    {
        return $this->hasCollected($userid,$itemid,$itemType,'favorite');
    }


/** Like 点赞功能 */

    /**
     * 点赞
     * like
     * @param  string       $userid
     * @param  string       $itemid
     * @param  string|null  $itemType
     * @param  int  $rate  比率可以用来标记喜欢、超级喜欢.
     *                     Rate can be used for mark like, super like.
     * @return ASResult
     */
    public function like( string $userid, string $itemid, string $itemType = null, int $rate = 1 ): ASResult
    {
        $checkCollect = $this->getCollectId( $userid,$itemid,$itemType,'like' );

        $data = static::initValuesFromArray([
            'type'=>'like',
            'userid'=>$userid,
            'itemid'=>$itemid,
            'itemtype'=>$itemType,
            'rate'=>$rate
        ]);

        if( $checkCollect->isSucceed() ){
            return $this->getDB()->increase( 'rate',static::table , static::uidCondition($checkCollect->getContent()), $rate );
        }else{
            return $this->add($data);
        }
    }

    /**
     * 不喜欢 (Like反向操作)
     * unLike
     * @param  string       $userid
     * @param  string       $itemid
     * @param  string|null  $itemType
     * @param  int          $rate
     * @return ASResult
     */
    public function unLike( string $userid, string $itemid, string $itemType = null, int $rate = 1 ): ASResult
    {
        return $this->like( $userid,$itemid,$itemType, 0 - $rate );
    }

    /**
     * 删除like数据
     * Remove Like data whatever like or unlike
     * @param  string       $userid
     * @param  string       $itemid
     * @param  string|null  $itemType
     * @return ASResult
     */
    public function cancelLike( string $userid, string $itemid, string $itemType = null ): ASResult
    {
        return $this->cancelWith($userid,$itemid,$itemType,'like');
    }

    /**
     * 查询是否点赞或不喜欢
     * Has Liked or unLiked
     * @param  string  $userid
     * @param  string  $itemid
     * @param  string  $itemType
     * @param  int     $rate
     *                      查询时通过 rate值同样可以判断是否进行了 不喜欢或 喜欢、超级喜欢
     *                      The rate value can also be used to determine whether unlike, like, or superlike is performed during the query.
     * @return bool
     */
    public function hasLiked( string $userid, string $itemid, string $itemType, int $rate = null ): bool
    {
        return $this->hasCollected($userid,$itemid,$itemType,'like',$rate);
    }


/** Follow 关注功能 */

    /**
     * 关注
     * follow
     * @param  string      $userid          用户id
     * @param  string      $followedUserid  被关注用户id
     * @param  array|null  $params          其他信息
     *                                         标题 title
     *                                         描述 description
     *                                         封面 cover
     *                                         更多 contents
     * @return ASResult
     */
    public function follow( string $userid, string $followedUserid, array $params = null ): ASResult
    {
        return $this->collect( $userid,$followedUserid,'user','follow', null, $params );
    }


    /**
     * 取消关注
     * unFollow
     * @param  string  $userid
     * @param  string  $followedUserid
     * @return ASResult
     */
    public function unFollow( string $userid, string $followedUserid ): ASResult
    {

        return $this->cancelWith($userid,$followedUserid,'user','follow');

    }

    /**
     * 是否已关注
     * hasFollowed
     * @param  string  $userid
     * @param  string  $followedUserid
     * @return bool
     */
    public function hasFollowed( string $userid, string $followedUserid ): bool
    {
        return $this->hasCollected($userid,$followedUserid,'user','follow');
    }


/** 分享记录功能 */

    /**
     * 分享
     * share
     * @param  string       $userid
     * @param  string       $itemid
     * @param  string|null  $itemType
     * @param  array|null   $contents
     * @return ASResult
     */
    public function share( string $userid, string $itemid, string $itemType = null, array $contents = null ): ASResult
    {

        $checkCollect = $this->getCollectId( $userid,$itemid,$itemType,'share' );

        $data = [
            'type'=>'like',
            'userid'=>$userid,
            'itemid'=>$itemid,
            'itemtype'=>$itemType,
            'contents'=>$contents
        ];

        if( $checkCollect->isSucceed() ){
            $updateRate = $this->getDB()->increase('rate',static::table,static::uidCondition($checkCollect->getContent()) ,1 );
            return isset($contents) ? $this->updateByArray(['contents'=>$contents],$checkCollect->getContent()) : $updateRate;
        }else{
            return $this->addByArray($data);
        }
    }

    /**
     * 是否分享过 或 分享次数超过rate
     * Check has Shared or shared more zan rate times
     * @param  string    $userid
     * @param  string    $itemid
     * @param  string    $itemType
     * @param  int|null  $rate
     * @return mixed
     */
    public function hasShared( string $userid, string $itemid, string $itemType, int $rate = null ): bool
    {
        return $this->hasCollected($userid,$itemid,$itemType,'share',$rate);
    }


}