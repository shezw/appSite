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


    // 计数系统收入
    public function countSystemIncome(array $filters)
    {

        $filters['payee'] = "system";

        return $this->count($filters);
    }

    // 计数系统支出
    public function countSystemExpend(array $filters)
    {

        $filters['payee'] = "system";

        return $this->count($filters);
    }

    // 系统收入列表
    public function listSystemIncome(array $filters, int $page = 1, int $size = 15, string $sort = null)
    {

        $filters['payee'] = "system";

        return $this->list($filters, $page, $size, $sort);
    }

    // 系统支出列表
    public function listSystemExpend(array $filters, int $page = 1, int $size = 15, string $sort = null)
    {

        $filters['payer'] = "system";

        return $this->list($filters, $page, $size, $sort);
    }


    // 获得账目统计
    public function totalSum(string $userid, string $identity = 'payee', int $starttime = null, int $endtime = null)
    {

        if (!$starttime || !$endtime) {
            return $this->error(10086, i18n('SYS_PARA_REQ'), 'FinanceDeal->totalIncome');
        }
        if ($starttime > $endtime) {
            return $this->take("{$starttime} -> {$endtime} ")->error(10086, i18n('SYS_LOGIC_ERR'), 'FinanceDeal->totalIncome');
        }

        $conditions = " $identity='{$userid}' AND createtime BETWEEN {$starttime} and {$endtime} ";

        $income = $this->getDB()->sum( 'amount',static::$table, $conditions);

        if (!$income->isSucceed()) {
            return $income;
        }

        return $this->take($income->getContent())->success(i18n('SYS_ANA_SUC'), 'FinanceDeal->totalIncome');
    }


    // 获得收入统计
    public function totalIncome(string $userid, int $starttime = null, int $endtime = null)
    {

        return $this->totalSum($userid, 'payee', $starttime ?? 1, $endtime ?? 9999999999);
    }

    // 获得支出统计
    public function totalExpend(string $userid, int $starttime = null, int $endtime = null)
    {

        return $this->totalSum($userid, 'payer', $starttime ?? 1, $endtime ?? 9999999999);
    }


    // 获得30日收入
    public function thirtyIncome(string $userid)
    {

        $time = new Time();
        return $this->totalSum($userid, 'payee', $time->today() - Time::THIRTY, $time->today());
    }

    // 获得30日 每日收入
    public function thirtyIncomeList(string $userid)
    {

        $time = new Time();

        for ($i = 30; $i > 0; $i--) {

            $list[] = $this->totalSum($userid, 'payee', $time->today() - (Time::DAY * $i), $time->today() - (Time::DAY * ($i - 1)))->getContent();

        }
        return $this->take($list)->success( i18n('SYS_GET_SUC'),  '$this->thirtyIncomeList');

    }


    // 获得30日支出
    public function thirtyExpend(string $userid)
    {

        $time = new Time();
        return $this->totalSum($userid, 'payer', $time->today() - Time::THIRTY, $time->today());
    }

    // 获得30日 每日支出
    public function thirtyExpendList(string $userid)
    {

        $time = new Time();

        for ($i = 30; $i > 0; $i--) {

            $list[] = $this->totalSum($userid, 'payer', $time->today() - (Time::DAY * $i), $time->today() - (Time::DAY * ($i - 1)))->getContent();

        }
        return $this->take($list)->success(i18n('SYS_GET_SUC'), '$this->thirtyExpendList');

    }


    // 获得7日收入
    public function weekIncome(string $userid)
    {

        $time = new Time();
        return $this->totalSum($userid, 'payee', $time->today() - Time::WEEK, $time->today());

    }

    // 获得7日支出
    public function weekExpend(string $userid)
    {

        $time = new Time();
        return $this->totalSum($userid, 'payer', $time->today() - Time::WEEK, $time->today());

    }

    // 获得昨日收入
    public function yesterdayIncome(string $userid)
    {

        $time = new Time();
        return $this->totalSum($userid, 'payee', $time->yesterday(), $time->today());

    }

    // 获得昨日支出
    public function yesterdayExpend(string $userid)
    {

        $time = new Time();
        return $this->totalSum($userid, 'payer', $time->yesterday(), $time->today());

    }

    // 获得当日收入
    public function todayIncome(string $userid)
    {

        $time = new Time();
        return $this->totalSum($userid, 'payee', $time->today(), $time->now);

    }

    // 获得当日支出
    public function todayExpend(string $userid)
    {

        $time = new Time();
        return $this->totalSum($userid, 'payer', $time->today(), $time->now);

    }


    protected static $record_enabled = true;

    public static $table     = "finance_deal";  // 表
    public static $primaryid = "uid";     // 主字段
    public static $addFields = [
        'payer',
        'payee',
        'type',
        'title',
        'pocket',
        'amount',
        'details',
        'status',
    ];      // 添加支持字段
    public static $updateFields = [
        'status',
    ];   // 更新支持字段
    public static $detailFields = "*";   // 详情支持字段
    public static $publicDetailFields = [
        'uid',
        'payer',
        'payee',
        'type',
        'title',
        'pocket',
        'amount',
        'details',
        'status',
        'createtime',
        'lasttime',
    ]; // 概览支持字段
    public static $overviewFields = [
        'uid',
        'payer',
        'payee',
        'type',
        'title',
        'pocket',
        'amount',
        // 'details',
        'status',
        'createtime',
        'lasttime',
    ]; // 概览支持字段
    public static $listFields = [
        'uid',
        'payer',
        'payee',
        'type',
        'title',
        'pocket',
        'amount',
        'status',
        'createtime',
        'lasttime',
    ];     // 列表支持字段
    public static $publicListFields = [
        'uid',
        'payer',
        'payee',
        'type',
        'title',
        'pocket',
        'amount',
        'status',
        'createtime',
        'lasttime',
    ];     // 开放接口列表支持字段
    public static $countFilters = [
        'uid',
        'payer',
        'payee',
        'type',
        'title',
        'pocket',
        'amount',
        'status',
        'createtime',
        'lasttime',
    ];
    public static $depthStruct = [
        'amount'=>'int',
        'createtime'=>'int',
        'lasttime'=>'int',
        'details'=>'ASJson'
    ];



}