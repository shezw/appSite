<?php
/**
 * Description
 * itemList.php
 */

namespace manager;

use APS\ASAPI;
use APS\ASResult;
use APS\Category;
use APS\Time;
use APS\User;

/**
 * 通用列表查询
 * itemList
 * @package manager
 */
class itemList extends ASAPI{

    const groupCharacterRequirement = [GroupRole_Super,GroupRole_Manager,GroupRole_Editor];
    const groupLevelRequirement = GroupLevel_Editor;
    const mode = ASAPI_Mode_Json;

    public function run(): ASResult
    {
        $itemClass = $this->params['itemClass'];
        $filters   = $this->params['filters'] ?? ['status'=>Status_Enabled] ;
        $page      = $this->params['page'] ?? 1;
        $size      = $this->params['size'] ?? 10;
        $order     = $this->params['order'] ?? 'createtime DESC';

        $getCount =  ($itemClass)::common()->countByArray($filters);
        $getList  =  ($itemClass)::common()->listByArray( $filters, $page, $size, $order);

        $list = [];
        $maxPage = (int)(($getCount->getContentOr(0) - 1 )/ $size + 1);
        $list['nav'] = $list['navigation'] = [
            'total'=> $getCount->getContentOr(0),
            'count'=> $maxPage,'max'=> $maxPage,
            'current'=> $page,'page'=> $page,
            'size'=> $size
        ];

        if( !$getList->isSucceed() ){
            return $this->take($list)->error(400,$getList->getMessage());
        }

        $list['list'] = $getList->getContent();

        for ( $i = 0; $i < count($list['list']); $i ++ ){

            if( isset($list['list'][$i]['authorid']) ){
                $author = User::common()->overview( $list['list'][$i]['authorid'] );
                $list['list'][$i]['author'] = $author->isSucceed() ? $author->getContent() : null;
                if(isset($list['list'][$i]['author'])) $list['list'][$i]['author']['avatar'] = $list['list'][$i]['author']['avatar'] ?? getConfig('defaultAvatar','WEBSITE');
            }

            if( isset($list['list'][$i]['userid']) ){
                $author = User::common()->overview( $list['list'][$i]['userid'] );
                $list['list'][$i]['user'] = $author->isSucceed() ? $author->getContent() : null;
                isset($list['list'][$i]['user']) && $list['list'][$i]['user']['avatar'] = $list['list'][$i]['user']['avatar'] ?? getConfig('defaultAvatar','MANAGER');
            }

            if( isset($list['list'][$i]['categoryid']) ){
                $category = Category::common()->detail($list['list'][$i]['categoryid']);
                $list['list'][$i]['category'] = $category->isSucceed() ? $category->getContent() : null;
            }

            if( isset($list['list'][$i]['status']) ){
                $list['list'][$i]['status_'] = i18n($list['list'][$i]['status'], 'status');
            }
            if( isset($list['list'][$i]['type']) ){
                $list['list'][$i]['type_'] = i18n($list['list'][$i]['type'], 'type');
            }

            $list['list'][$i]['createtime_'] = Time::common($list['list'][$i]['createtime'])->humanityOutput();
            $list['list'][$i]['lasttime_'] = Time::common($list['list'][$i]['lasttime'])->humanityOutput();

            $list['list'][$i]['itemid'] = $list['list'][$i][ $itemClass::primaryid ];
        }

        return $this->take($list)->success();
    }


}