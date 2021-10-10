<?php

namespace APS;

/**
 * 财务 - 交易
 * CommerceDeal
 *
    uid  交易ID
    payer   支付方ID
    payee   收款方ID
    type    交易类型 默认 佣金     commission 佣金 transmission 转账 bonus 系统奖励...
    pocket  钱包类型 默认 账户余额  balance 账户余额  point 积分
    title   标题 30字以内
    details 详情 不限 k-v json
    amount  总价 精度0.01元  decimal 12,2
    status  状态  等待入账 waiting  used 已使用  done 完成
 *
 * @package APS\service
 */
class FinanceDeal extends ASModel{

    const record_enabled = true;

    const table     = "finance_deal";
    const comment   = '财务-交易';
    const primaryid = "uid";
    const addFields = [
        'saasid','payer', 'payee', 'type',
        'title', 'pocket', 'amount',
        'details',
        'status','createtime','lasttime',
    ];
    const updateFields = [
        'status',
    ];
    const detailFields = [
        'uid', 'saasid', 'payer', 'payee', 'type',
        'title', 'pocket', 'amount',
        'details',
        'status','createtime','lasttime',
    ];
    const publicDetailFields = [
        'uid', 'saasid', 'payer', 'payee', 'type',
        'title', 'pocket', 'amount',
        'details',
        'status',
        'createtime',
        'lasttime',
    ];
    const overviewFields = [
        'uid', 'saasid', 'payer', 'payee', 'type',
        'title', 'pocket', 'amount',

        'status',
        'createtime',
        'lasttime',
    ];
    const listFields = [
        'uid', 'saasid', 'payer', 'payee', 'type',
        'title', 'pocket', 'amount',
        'status', 'createtime', 'lasttime',
    ];
    const publicListFields = [
        'uid', 'saasid', 'payer', 'payee', 'type',
        'title', 'pocket', 'amount',
        'status', 'createtime', 'lasttime',
    ];
    const filterFields = [
        'uid', 'saasid', 'payer', 'payee', 'type',
        'title', 'pocket', 'amount',
        'status','createtime','lasttime',
    ];
    const depthStruct = [
        'amount'=>DBField_Decimal,
        'details'=>DBField_Json,
        'createtime'=>DBField_TimeStamp,
        'lasttime'=>DBField_TimeStamp,
    ];

    const tableStruct = [

        'uid'=>      ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'交易ID' ,     'idx'=>DBIndex_Index ],
        'saasid'=>   ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'所属saas',    'idx'=>DBIndex_Index,],
        'payer'=>    ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'支付方ID' ,   'idx'=>DBIndex_Index ],
        'payee'=>    ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'收款方ID' ,   'idx'=>DBIndex_Index ],

        'type'=>     ['type'=>DBField_String,    'len'=>16,  'nullable'=>1,  'cmt'=>'交易类型 默认 佣金 ' ],
        // eg: Type_Commission 佣金 Type_Transmission 转账 Type_Bonus 系统奖励...
        'pocket'=>   ['type'=>DBField_String,    'len'=>24,  'nullable'=>0,  'cmt'=>'钱包类型 默认 账户余额 ' , 'dft'=>'balance',      'idx'=>DBIndex_Index ],
        // eg: Type_balance 账户余额  Type_point 积分

        'title'=>    ['type'=>DBField_String,    'len'=>63,  'nullable'=>0,  'cmt'=>'标题 30字以内' ,     'idx'=>DBIndex_FullText ],
        'details'=>  ['type'=>DBField_Json,      'len'=>-1,  'nullable'=>1,  'cmt'=>'详情 不限 k-v json' ],
        'amount'=>   ['type'=>DBField_Decimal,   'len'=>'12,5',  'nullable'=>0,  'cmt'=>'总价 精度0.01元' ,   'dft'=>0,       ],
        'status'=>   ['type'=>DBField_String,    'len'=>24,  'nullable'=>0,  'cmt'=>'状态 等待入账waiting,已使用used',        'dft'=>'enabled', ],

        'createtime'   =>['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'创建时间',                      'idx'=>DBIndex_Index, ],
        'lasttime'     =>['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'上一次更新时间', ],
    ];



    # 计数系统收入
    public function countSystemIncome(DBConditions $filters): ASResult
    {
        $filters->and('payee')->equal('system');

        return $this->count($filters);
    }

    # 计数系统支出
    public function countSystemExpend(DBConditions $filters): ASResult
    {
        $filters->and('payee')->equal('system');

        return $this->count($filters);
    }

    # 系统收入列表
    public function listSystemIncome(DBConditions $filters, int $page = 1, int $size = 15, string $sort = null): ASResult
    {
        $filters->and('payee')->equal('system');

        return $this->list($filters, $page, $size, $sort);
    }

    # 系统支出列表
    public function listSystemExpend(DBConditions $filters, int $page = 1, int $size = 15, string $sort = null): ASResult
    {
        $filters->and('payee')->equal('system');

        return $this->list($filters, $page, $size, $sort);
    }


## Common sum
## 通用求和

    # 获得账目统计
    public function totalSum(string $userid = null, string $identity = 'payee', int $starttime = null, int $endtime = null, string $pocket = 'balance', string $type = null ):ASResult
    {
        if (!$starttime || !$endtime) {
            return $this->error(10086, i18n('SYS_PARA_REQ'), 'FinanceDeal->totalIncome');
        }
        if ($starttime > $endtime) {
            return $this->take("{$starttime} -> {$endtime} ")->error(10086, i18n('SYS_LOGIC_ERR'), 'FinanceDeal->totalIncome');
        }

        $conditions = DBConditions::init(static::table)
            ->and($identity)->equalIf($userid)
            ->and('pocket')->equal($pocket)
            ->and('type')->equalIf($type)
            ->and('createtime')->between($starttime,$endtime);

        $income = $this->getDB()->get(DBFields::init('amount')->sumAs('total'),static::table, $conditions);

        if (!$income->isSucceed()) {
            return $income;
        }

        return $this->take($income->getContent())->success(i18n('SYS_ANA_SUC'), 'FinanceDeal->totalIncome');
    }

    # 通用 积分统计 Point
    public function totalPoint( string $userid = null, string $identity = 'payee',  int $starttime = 0, int $endtime = 9999999999, string $type = null): ASResult
    {
        return $this->totalSum($userid, $identity,$starttime,$endtime,'point', $type );
    }

    # 通用 增长统计
    public function totalIncrease( string $userid = null, string $identity = 'payee', int $starttime = null, int $endtime = null, string $pocket = 'balance', string $type = null ):ASResult
    {
        return $this->totalSum( $userid,$identity,$starttime,$endtime,$pocket,$type );
    }

    # 通用 减少统计
    public function totalDecrease( string $userid = null, string $identity = 'payee', int $starttime = null, int $endtime = null, string $pocket = 'balance', string $type = null ):ASResult
    {
        return $this->totalSum( $userid,$identity,$starttime,$endtime,$pocket,$type );
    }


    # 收入统计 Balance
    public function totalIncome(string $userid = null, int $starttime = 0, int $endtime = 9999999999, string $type = null):ASResult
    {
        return $this->totalIncrease($userid, 'payee', $starttime, $endtime, 'balance', $type);
    }

    # 支出统计 Balance
    public function totalExpend(string $userid = null, int $starttime = 0, int $endtime = 9999999999, string $type = null):ASResult
    {
        return $this->totalSum($userid, 'payer', $starttime, $endtime, 'pocket', $type );
    }

    public function totalPointIncrease( string $userid = null, int $starttime = 0, int $endtime = 9999999999, string $type = 'bonus' ): ASResult
    {
        return $this->totalIncrease( $userid,'payee',$starttime,$endtime,'point', $type );
    }

    public function totalPointDecrease( string $userid = null, int $starttime = 0, int $endtime = 9999999999, string $type = 'bonus' ): ASResult
    {
        return $this->totalDecrease( $userid,'payer',$starttime,$endtime,'point', $type );
    }


## Duration sum
## 阶段性求和

    const AnalysisMonth = 30;
    const AnalysisWeek  = 7;
    const AnalysisYesterday = -1;
    const AnalysisToday     = 1;

    public function durationSum( int $AnalysisDuration, string $userid = null, string $identity = 'payee', string $pocket = 'balance', string $type = null ): ASResult
    {
        $time = new Time();
        $start = 0;
        $end   = 0;

        switch ( $AnalysisDuration ){
            case static::AnalysisMonth:
                $start = $time->today() - Time::THIRTY;
                $end   = $time->today();
                break;
            case static::AnalysisWeek:
                $start = $time->today() - Time::WEEK;
                $end   = $time->today();
                break;
            case static::AnalysisYesterday:
                $start = $time->yesterday();
                $end   = $time->today();
                break;
            case static::AnalysisToday:
                $start = $time->today();
                $end   = $time->now;
                break;
        }

        return $this->totalSum( $userid, $identity, $start,$end, $pocket, $type );
    }


    # 获得30日收入
    public function thirtyIncrease(string $userid = null, string $pocket = 'balance', string $type = null):ASResult
    {
        return $this->durationSum( static::AnalysisMonth, $userid, 'payee', $pocket, $type );
    }

    # 获得30日支出
    public function thirtyDecrease(string $userid = null, string $pocket = 'balance', string $type = null):ASResult
    {
        return $this->durationSum( static::AnalysisMonth, $userid, 'payer', $pocket, $type );
    }

    # 获得7日收入
    public function weekIncrease(string $userid = null, string $pocket = 'balance', string $type = null):ASResult
    {
        return $this->durationSum( static::AnalysisWeek, $userid, 'payee', $pocket, $type );
    }

    # 获得7日支出
    public function weekDecrease(string $userid = null, string $pocket = 'balance', string $type = null):ASResult
    {
        return $this->durationSum( static::AnalysisWeek, $userid, 'payer', $pocket, $type );
    }

    # 获得昨日收入
    public function yesterdayIncrease(string $userid = null, string $pocket = 'balance', string $type = null):ASResult
    {
        return $this->durationSum( static::AnalysisYesterday, $userid, 'payee', $pocket, $type );
    }

    # 获得昨日支出
    public function yesterdayDecrease(string $userid = null, string $pocket = 'balance', string $type = null):ASResult
    {
        return $this->durationSum( static::AnalysisYesterday, $userid, 'payer', $pocket, $type );
    }

    # 获得当日收入
    public function todayIncrease(string $userid = null, string $pocket = 'balance', string $type = null):ASResult
    {
        return $this->durationSum( static::AnalysisToday, $userid, 'payee', $pocket, $type );
    }

    # 获得当日支出
    public function todayDecrease(string $userid = null, string $pocket = 'balance', string $type = null):ASResult
    {
        return $this->durationSum( static::AnalysisToday, $userid, 'payer', $pocket, $type );
    }

    const AnalysisStepMin  = 60;
    const AnalysisStepHour = 3600;
    const AnalysisStepDay  = 86400;
    const AnalysisStepWeek = 604800;

    public function sumAnalysis( string $userid = null, string $identity = 'payee', int $starttime = 0, int $endtime = 99999999, int $AnalysisStep = 86400, string $pocket = 'balance', string $type = null ):ASResult
    {
        $list = [];
        $t  = $starttime;

        $timer  = new Time();
        if ($AnalysisStep>=86400){ $format = 'Y-m-d'; }else if($AnalysisStep>=3600){ $format = 'm-d H:00'; }else{ $format = 'm-d H:i'; }

        $loops = (int)($endtime-$starttime)/$AnalysisStep ;

        while( $t < $endtime ){

            $list[] = [
                'time'=>$t,
                'time_'=>$timer->customOutput( $format,$t ),
                'sum'=>$this->totalSum( $userid, $identity, $t, $t+$AnalysisStep, $pocket, $type )
            ];

            $t += $AnalysisStep;
        }
        return $this->take($list)->success( i18n('SYS_GET_SUC'),  '$this->thirtyIncomeList');
    }


    # 获得30日 每日收入
    public function thirtyIncreaseList(string $userid = null, string $pocket = 'balance', string $type = null):ASResult
    {
        $time = new Time();
        $list = [];

        for ($i = 30; $i > 0; $i--) {

            $list[] = $this->totalIncrease($userid, $time->today() - (Time::DAY * $i), $time->today() - (Time::DAY * ($i - 1)))->getContent();
        }
        return $this->take($list)->success( i18n('SYS_GET_SUC'),  '$this->thirtyIncomeList');
    }


    # 获得30日 每日支出
    public function thirtyDecreaseList(string $userid = null, string $pocket = 'balance', string $type = null):ASResult
    {
        $time = new Time();
        $list = [];

        for ($i = 30; $i > 0; $i--) {

            $list[] = $this->totalDecrease($userid,  $time->today() - (Time::DAY * $i), $time->today() - (Time::DAY * ($i - 1)))->getContent();
        }
        return $this->take($list)->success(i18n('SYS_GET_SUC'), '$this->thirtyExpendList');
    }





## Analysis


    public function pointIncreaseAnalysis( string $userid = null, int $starttime = 0 , int $endtime = 9999999999 , int $step = 86400, string $type = null ): ASResult
    {
        return $this->sumAnalysis( $userid, 'payee', $starttime,$endtime,$step, 'point', $type );
    }

    public function pointDecreaseAnalysis( string $userid = null, int $starttime = 0 , int $endtime = 9999999999 , int $step = 86400, string $type = null ): ASResult
    {
        return $this->sumAnalysis( $userid, 'payer', $starttime,$endtime,$step, 'point', $type );
    }

    public function increaseOriginAnalysis( int $starttime = 0 , int $endtime = 9999999999, int $page = 1, int $size = 50, bool $asc = false ): ASResult
    {

        $fields = DBFields::allOf(static::table)->and('amount')->sumAs('total');

        $conditions = DBConditions::init(static::table)
            ->where('createtime')->between($starttime,$endtime)
            ->and('amount')->bigger(0);

        $conditions->groupBy('payer');
        $conditions->limitWith( $size * ($page - 1), $size );

        $conditions->orderWith( $asc ? 'total ASC' : 'total DESC' );

        return $this->getDB()->get( $fields, static::table, $conditions );
    }

    public function decreaseOriginAnalysis( int $starttime = 0 , int $endtime = 9999999999, int $page = 1, int $size = 50, bool $asc = false ): ASResult
    {
        $fields = DBFields::allOf(static::table)->and('amount')->sumAs('total');

        $conditions = DBConditions::init(static::table)
            ->where('createtime')->between($starttime,$endtime)
            ->and('amount')->less(0);

        $conditions->groupBy('payee');
        $conditions->limitWith( $size * ($page - 1), $size );

        $conditions->orderWith( $asc ? 'total ASC' : 'total DESC' );

        return $this->getDB()->get( $fields, static::table, $conditions );
    }

}