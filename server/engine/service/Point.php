<?php

namespace APS;

/**
 * 积分系统 基于UserPocket
 * Point
 *
    title 机制名称 Name of rule
    description 机制描述  Description of rule
    value 奖励积分  bonus point value
    limit 全局限制  Global limit
    daily 每日限制  Daily limit

    全局限制优先级高于每日限制
    Global limit is higher level than daily

    可以同时存在 优先计算全局限制,全局限制触顶后不再计算
 *
 * @package APS
 */
class Point extends ASObject {

    public function bonus( string $userid, string $rule ): ASResult
    {
        $getRuleSetting = _ASSetting()->read($rule,'POINTBONUS_RULES');

        if( $getRuleSetting->isSucceed() ){ return $getRuleSetting; }

        $ruleSetting = $getRuleSetting->getContent();

        $title = $ruleSetting['title'];
        $amount= $ruleSetting['value'];
        $desc  = $ruleSetting['description'];

        $time  = new Time();
        $today = $time->today();

        # 检测限制
        if (isset($ruleSetting['limit'])||isset($ruleSetting['daily'])) {

            $limit = isset($ruleSetting['limit']) ? $ruleSetting['limit'] : $ruleSetting['daily'];

            $filter = DBConditions::init()->where('payee')->equal($userid)->and('type')->equal('bonus')->and('payer')->equal($rule);

            if (isset($ruleSetting['daily'])) {
                $filter->and('createtime')->bigger($today);
            }
            $count = FinanceDeal::common()->count($filter)->getContent();

            if($limit<=$count){
                return $this->take($count)->error(900,'已经达到上限','POINT::bonus');
            }
        }
        return $this->increase($userid,$amount,$rule,'bonus',$title,$desc);
    }

    public function increase( string $userid, int $amount,string $payer='system', string $type ='income', string $title= null ,$description= null ):ASResult {

        $increase = UserPocket::common()->increase('point',UserPocket::uidCondition($userid),$amount);
        if($increase->isSucceed()){
            FinanceDeal::common()->addByArray([
                'type'=>$type,
                'title'=>$title,
                'payer'=>$payer,
                'payee'=>$userid,
                'amount'=>$amount,
                'pocket'=>'point',
                'details'=>['description'=>$description],
            ]);
        }
        return $this->take($amount)->success(i18n('SYS_SUC'),'Point->increase');
    }

    public function decrease( string $userid, int $amount, string $type ='decrease', string $title = null , $description = null  ):ASResult {

        $userPocket = new UserPocket($userid);

        if(!$userPocket->enough($amount,'point')){
            $DB = $userPocket->clear('point');
        }else{
            $DB = $userPocket->decrease('point', UserPocket::uidCondition($userid),$amount );

            if($DB->isSucceed()){
                FinanceDeal::common()->addByArray([
                    'type'=>$type,
                    'title'=>$title,
                    'payer'=>$userid,
                    'payee'=>'system',
                    'amount'=>$amount,
                    'pocket'=>'point',
                    'details'=>['description'=>$description],
                ]);
            }
        }
        if (!$DB->isSucceed()){ return $DB; }

        return $this->take($amount)->success(i18n('DECREASE_SUC'),'Point->increase');
    }


}