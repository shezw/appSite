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
     * @return \APS\ASResult
     */
    public function collect( string $userid, string $itemid, string $itemType = null, string $type = 'like', int $rate = null, array $extraInformation = null ){

        $checkCollect = $this->getCollectId( $userid,$itemid,$itemType,$type );
        $extraInformation = isset($extraInformation) ? Filter::purify( $extraInformation, static::$addFields ) : [];

        $data = [
            'type'=>$type,
            'userid'=>$userid,
            'itemid'=>$itemid,
            'itemtype'=>$itemType,
            'rate'=>$rate
        ];
        $data = array_merge($data,$extraInformation);

        if( $checkCollect->isSucceed() ){
            return $this->update( $data, $checkCollect->getContent() );
        }else{
            return $this->add($data);
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
    public function hasCollected( string $userid, string $itemid, string $itemType = null, string $type='like', int $rate = null){

        return $this->getDB()->count(static::$table,[
            'type'=>$type,
            'userid'=>$userid,
            'itemid'=>$itemid,
            'itemtype'=>$itemType,
            'rate'=>$rate
        ])->getContent() > 0;
    }

    /**
     * 取消收藏
     * cancel
     * @param  string       $userid
     * @param  string       $itemid
     * @param  string|null  $itemType
     * @param  string       $type
     * @return \APS\ASResult
     */
    public function cancelWith( string $userid, string $itemid, string $itemType = null , string $type='like' ){

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
     * @return \APS\ASResult
     */
    public function getCollectId( string $userid, string $itemid, string $itemType = null, string $type='like' ){

        $getCollectList = $this->list([
            'type'=>$type,
            'userid'=>$userid,
            'itemid'=>$itemid,
            'itemtype'=>$itemType],1,1,'createtime DESC');

        if( !$getCollectList->isSucceed() ){
            return $this->error(400,i18n('SYS_GET_FAL'));
        }
        return $this->take($getCollectList->getContent()[0]['collectid'])->success();
    }

/** Favorite 收藏功能 */

    /**
     * 添加收藏
     * favorite
     * @param  string       $userid
     * @param  string       $itemid
     * @param  string|null  $itemType
     * @param  array|null   $params            更多
     *                                         标题 title
     *                                         描述 description
     *                                         封面 cover
     *                                         更多 contents
     * @return \APS\ASResult
     */
    public function favorite( string $userid, string $itemid, string $itemType = null, array $params = null ){

        return $this->collect( $userid,$itemid,$itemType,'favorite', null, $params );
    }

    // 取消收藏
    public function unFavorite( string $userid, string $itemid, string $itemType = null ){

        return $this->cancelWith($userid,$itemid,$itemType,'favorite');
    }

    public function hasFavorited( string $userid, string $itemid, string $itemType ){
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
     * @return \APS\ASResult
     */
    public function like( string $userid, string $itemid, string $itemType = null, int $rate = 1 ){

        $checkCollect = $this->getCollectId( $userid,$itemid,$itemType,'like' );

        $data = [
            'type'=>'like',
            'userid'=>$userid,
            'itemid'=>$itemid,
            'itemtype'=>$itemType,
            'rate'=>$rate
        ];

        if( $checkCollect->isSucceed() ){
            return $this->getDB()->increase( 'rate',$rate, static::$table , $checkCollect->getContent() );
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
     * @return \APS\ASResult
     */
    public function unLike( string $userid, string $itemid, string $itemType = null, int $rate = 1 ){
        return $this->like( $userid,$itemid,$itemType, 0 - $rate );
    }

    /**
     * 删除like数据
     * Remove Like data whatever like or unlike
     * @param  string       $userid
     * @param  string       $itemid
     * @param  string|null  $itemType
     * @return \APS\ASResult
     */
    public function cancelLike( string $userid, string $itemid, string $itemType = null ){

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
    public function hasLiked( string $userid, string $itemid, string $itemType, int $rate = null ){
        return $this::hasCollected($userid,$itemid,$itemType,'like',$rate);
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
     * @return \APS\ASResult
     */
    public function follow( string $userid, string $followedUserid, array $params = null ){

        return $this->collect( $userid,$followedUserid,'user','follow', null, $params );
    }


    /**
     * 取消关注
     * unFollow
     * @param  string  $userid
     * @param  string  $followedUserid
     * @return \APS\ASResult
     */
    public function unFollow( string $userid, string $followedUserid ){

        return $this->cancelWith($userid,$followedUserid,'user','follow');

    }

    /**
     * 是否已关注
     * hasFollowed
     * @param  string  $userid
     * @param  string  $followedUserid
     * @return bool
     */
    public function hasFollowed( string $userid, string $followedUserid ){
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
     * @return \APS\ASResult
     */
    public function share( string $userid, string $itemid, string $itemType = null, array $contents = null ){

        $checkCollect = $this->getCollectId( $userid,$itemid,$itemType,'share' );

        $data = [
            'type'=>'like',
            'userid'=>$userid,
            'itemid'=>$itemid,
            'itemtype'=>$itemType,
            'contents'=>$contents
        ];

        if( $checkCollect->isSucceed() ){
            $updateRate = $this->getDB()->increase( 'rate', 1 , static::$table , $checkCollect->getContent() );
            return isset($contents) ? $this->update(['contents'=>$contents],$checkCollect->getContent()) : $updateRate;
        }else{
            return $this->add($data);
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
    public function hasShared( string $userid, string $itemid, string $itemType, int $rate = null ){
        return $this->hasCollected($userid,$itemid,$itemType,'share',$rate);
    }


    public static $table     = "user_collect";  // 表
    public static $primaryid = "collectid";     // 主字段
    public static $addFields = [
        'userid',
        'type',
        'itemid',
        'itemtype',
        'title',
        'cover',
        'description',
        'contents',
        'rate',
        'status',
    ];      // 添加支持字段
    public static $updateFields = [
        'rate',
        'status',
    ];   // 更新支持字段
    public static $detailFields = "*";   // 详情支持字段
    public static $overviewFields = [
        'userid',
        'type',
        'itemid',
        'itemtype',
        'title',
        'cover',
        'description',
        'contents',
        'rate',
        'status',
        'createtime',
        'lasttime',
    ]; // 概览支持字段
    public static $listFields = [
        'userid',
        'type',
        'itemid',
        'itemtype',
        'title',
        'cover',
        'description',
        'contents',
        'rate',
        'status',
        'createtime',
        'lasttime',
    ];     // 列表支持字段
    public static $countFilters = [
        'userid',
        'type',
        'itemid',
        'itemtype',
        'title',
        'rate',
        'status',
        'createtime',
        'lasttime',
    ];
    public static $depthStruct = [
        'rate'=>'int',
        'createtime'=>'int',
        'lasttime'=>'int',
        'contents'=>'ASJson'
    ];

}