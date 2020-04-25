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
class Point extends ASModel {

    public function bonus( string $userid, string $rule ){

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
            $param = ['payee'=>$userid,'type'=>'bonus','payer'=>$rule];
            if (isset($ruleSetting['daily'])) {
                $param['createtime']="[[>]]{$today}";
            }
            $count = FinanceDeal::common()->count($param)->getContent();

            if($limit<=$count){
                return $this->take($count)->error(900,'已经达到上限','POINT::bonus');
            }
        }

        return POINT::increase($userid,$amount,$rule,'bonus',$title,$desc);

    }




    public function increase( string $userid, int $amount,string $payer='system', string $type ='income', string $title= NULL ,$description= NULL ){

        $increase = POCKET::increase($userid,$amount,'point');
        if(RESULT::isSucceed($increase)){
            $DB = DEAL::add([
                'type'=>$type,
                'title'=>$title,
                'payer'=>$payer,
                'payee'=>$userid,
                'amount'=>$amount,
                'pocket'=>'point',
                'details'=>['description'=>$description],
            ]);
        }

        return RESULT::feedback(0,'增长成功',$amount,'POINT::increase');
    }

    public function decrease( string $userid, int $amount, string $type ='decrease', string $title = NULL , $description = NULL  ){

        if(!POCKET::enough($userid,$amount,'point')){
            $DB = POCKET::clear($userid,'point');
        }else{
            $DB = POCKET::decrease($userid,$amount,'point',$title,$description);
        }

        if (!RESULT::isSucceed($DB)){ return $DB; }

        return RESULT::feedback(0,'扣除成功',$amount,'POINT::decrease');

    }


// additionAnalysis
// reduceAnalysis

    public static function pointAddition( int $starttime = 0, int $endtime = 9999999999 ){

        $conditions = "createtime>=$starttime AND createtime<=$endtime AND pocket='point' AND amount>0 ";

        return $GLOBALS['sql']->sum(['field'=>'amount','table'=>'finance_deal','conditions'=>$conditions]);

    }

    public static function additionAnalysis( int $starttime = 0 , int $endtime = 9999999999 , int $duration = 86400 ){

        $time  = new TIME();
        if ($duration>=86400){ $format = 'Y-m-d'; }else if($duration>=3600){ $format = 'm-d H:00'; }else{ $format = 'm-d H:i'; }

        $loops = (int)($endtime-$starttime)/$duration ;

        $analysis = [];

        for ($i=$loops; $i >=0 ; $i--) {
            $t = $endtime-$i*$duration;
            $analysis[] = [
                'time'=>$t,
                'time_'=>$time->customDatetime($t,$format),
                'sum'=>POINT::pointAddition($t,$t+$duration)['content']['sum'],
            ];
        }

        return RESULT::feedback(0,'统计成功',$analysis,'POINT::additionAnalysis');

    }


    public static function pointReduce( int $starttime = 0, int $endtime = 9999999999 ){

        $conditions = "createtime>=$starttime AND createtime<=$endtime AND pocket='point' AND amount<0 ";

        return $GLOBALS['sql']->sum(['field'=>'amount','table'=>'finance_deal','conditions'=>$conditions]);

    }

    public static function reduceAnalysis( int $starttime = 0 , int $endtime = 9999999999 , int $duration = 86400 ){

        $time  = new TIME();
        if ($duration>=86400){ $format = 'Y-m-d'; }else if($duration>=3600){ $format = 'm-d H:00'; }else{ $format = 'm-d H:i'; }

        $loops = (int)($endtime-$starttime)/$duration ;

        $analysis = [];

        for ($i=$loops; $i >=0 ; $i--) {
            $t = $endtime-$i*$duration;
            $analysis[] = [
                'time'=>$t,
                'time_'=>$time->customDatetime($t,$format),
                'sum'=>POINT::pointReduce($t,$t+$duration)['content']['sum'],
            ];
        }

        return RESULT::feedback(0,'统计成功',$analysis,'POINT::additionAnalysis');

    }


    // 积分总量
    public static function pointTotal( ){

        return $GLOBALS['sql']->sum(['field'=>'point','table'=>'user_pocket',]);

    }

    // 积分总消耗
    public static function pointTotalReduce( ){

        return $GLOBALS['sql']->sum(['field'=>'amount','table'=>'finance_deal','conditions'=>'amount<0']);

    }


    public static function additionOriginAnalysis( int $starttime = 0 , int $endtime = 9999999999 ){

        $conditions = "createtime>=$starttime AND createtime<=$endtime AND amount>0";
        $count = $GLOBALS['sql']->sumByGroup(['group'=>'payer','fields'=>['payer'],'key'=>['amount'],'table'=>'finance_deal','conditions'=>$conditions]);

        if (!RESULT::isSucceed($count)) { return $count; }

        for ($i=0; $i < count($count['content']); $i++) {
            $count['content'][$i]['payer_'] = LOCATE::exchange('payer',$count['content'][$i]['payer']);
        }

        return RESULT::feedback(0,'统计成功',$count['content'],'DEAL::totalIncome');

    }

    public static function reduceOriginAnalysis( int $starttime = 0 , int $endtime = 9999999999 ){

        $conditions = "createtime>=$starttime AND createtime<=$endtime AND amount<0";
        $count = $GLOBALS['sql']->sumByGroup(['group'=>'title','fields'=>['title'],'key'=>['amount'],'table'=>'finance_deal','conditions'=>$conditions]);

        if (!RESULT::isSucceed($count)) { return $count; }

        // for ($i=0; $i < count($count['content']); $i++) {
        // $count['content'][$i]['type_']  = LOCATE::translate($count['content'][$i]['type']);
        // $count['content'][$i]['payer_'] = LOCATE::exchange('payer',$count['content'][$i]['payer']);
        // }

        return RESULT::feedback(0,'统计成功',$count['content'],'DEAL::totalIncome');

    }


}