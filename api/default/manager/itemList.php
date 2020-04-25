<?php
/**
 * Description
 * itemList.php
 */

namespace manager;

use APS\ASResult;
use APS\Category;
use APS\Time;
use APS\User;

/**
 * 通用列表查询
 * itemList
 * @package manager
 */
class itemList extends \APS\ASAPI{

    private $itemClass = '\APS\ASModel';
    private $filters   = ['status'=>'enabled'];
    private $page = 1;
    private $size = 20;
    private $sort   = 'createtime DESC';

    protected static $groupCharacterRequirement = ['super','manager','editor'];
    protected static $groupLevelRequirement = 40000;

    public function run(): ASResult
    {
        $this->itemClass = $this->params['itemClass'];
        $this->filters   = $this->params['filters'] ?? ['status'=>'enabled'] ;
        $this->page      = $this->params['page'] ?? 1;
        $this->size      = $this->params['size'] ?? 10;
        $this->sort      = $this->params['sort'] ?? 'createtime DESC';

        $getCount =  ($this->itemClass)::common()->count( $this->filters ) ?? ASResult::shared();
        $getList  =  ($this->itemClass)::common()->list( $this->filters, $this->page, $this->size, $this->sort ) ?? ASResult::shared();

        $list = [];
        $maxPage = (int)(($getCount->getContent() - 1 )/ $this->size + 1);
        $list['nav'] = $list['navigation'] = [
            'total'=> $getCount->getContent(),
            'count'=> $maxPage,'max'=> $maxPage,
            'current'=>$this->page,'page'=>$this->page,
            'size'=>$this->size
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
                $list['list'][$i]['status_'] = i18n($list['list'][$i]['status']);
            }
            if( isset($list['list'][$i]['type']) ){
                $list['list'][$i]['type_'] = i18n($list['list'][$i]['type']);
            }

            $list['list'][$i]['createtime_'] = Time::common($list['list'][$i]['createtime'])->humanityOutput();
            $list['list'][$i]['lasttime_'] = Time::common($list['list'][$i]['lasttime'])->humanityOutput();

            $list['list'][$i]['itemid'] = $list['list'][$i][ $this->itemClass::$primaryid ];
        }

        return $this->take($list)->success();
    }


}