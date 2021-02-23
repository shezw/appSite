<?php

namespace APS;

/**
 * 电商 - 订单
 * CommerceOrder
 *

    订单-支付流程描述:
    订单 ORDER
    支付 PAYMENT

    0 查询订单
    0.1 检测订单状态 ( pending 等待中 needpay 待支付  needcall 待回调  expired 已过期  done 已完成  [ 暂不考虑物流 如考虑需要加入 运输中等状态 ] 或增加物流表 )
    0.1.1 待支付 -> 2
    0.1.2 待回调 -> 3
    0.1.3 已过期 -> 4.3

    1 生成订单表单

    2 生成支付表单
    2.0 检测表单状态
    2.0.1 未过期,待支付 -> 2.1
    2.0.2 已过期 -> 5.3 -> 2
    2.1 请求支付接口
    2.1.1 请求完成,等待支付接口回调
    2.1.2 请求失败(由支付接口发起失败) -> 5.2

    2.2.0 未回调
    2.2.1 回调支付完成 -> 5.1 -> 3
    2.2.2 回调支付失败(由系统内部发起失败) -> 5.2

    3 回调订单表单
    3.1 检测订单状态
    3.1.1 待回调,进行回调
    3.1.2 无需回调,系统标记 (+错误日志)
    3.2 执行回调
    3.2.1 订单回调完成,更新订单状态
    3.2.2 订单回调失败,标记订单状态 (+错误日志)

    4 更新订单状态
    4.1 完成
    4.2 过期
    4.3 核销

    5 更新支付状态
    5.1 完成
    5.2 失败
    5.3 过期

 *
 * @package APS\service\Commerce
 */
class CommerceOrder extends ASModel{

/** 实例化属性 */

    /**
     * @var string
     */
    public $uid;
    /**
     * @var string
     */
    public $userid;
    /**
     * @var string
     */
    public $areaid;

    /**
     * @var string
     */
    public $title;
    /**
     * @var string
     */
    public $cover;

    /**
     * @var float
     */
    public $amount;
    /**
     * @var string
     */
    public $itemid;
    /**
     * @var string
     */
    public $itemtype;
    /**
     * @var int
     */
    public $quantity;

    /**
     * @var string
     */
    public $payment;
    /**
     * @var string
     */
    public $paymentid;
    /**
     * @var string
     */
    public $promoterid;
    /**
     * @var int
     */
    public $freeorder;

    public $details;
    /**
     * @var string
     * @mark needpay待支付, pending等待中, shipping物流中, precancel取消中, needconfirm待审核, done已完成, error错误, closed已关闭, canceled已取消
     */
    public $status;

    /**
     * @var int
     */
    public $writeoff;

    /**
     * @var string
     */
    public $ticket;
    /**
     * @var string
     */
    public $idnumber;

    /**
     * @var int
     */
    public $expire;

    /**
     * @var int
     */
    public $refundexpire;
    /**
     * @var int
     */
    public $refundrequesttime;
    /**
     * @var string
     */
    public $refundmessage;

    /**
     * Description
     * @var array | null
     */
    private $callback;

    /**
     * Description
     * @var array | null
     */
    private $refundcallback;

    /**
     * Description
     * @var array | null
     */
    private $breachcallback;

    private $instantiated = false;

    /**
     * 实例化
     * instance
     * @param $uid
     * @return \APS\CommerceOrder
     */
    public static function instance( string $uid ): CommerceOrder
    {

        $order = static::common();
        $order->initDetail($uid);
        return $order;
    }


    /**
     * 实例化数据
     * initDetail
     * @param  string  $uid
     * @return ASResult
     */
    public function initDetail( string $uid ):ASResult{
        if( $this->instantiated ){ return $this->success(); }
        $getDetail = $this->detail( $uid );
        if( !$getDetail->isSucceed() ){ return $getDetail; }

        $detailArray = $getDetail->getContent();

        $this->uid = $detailArray['uid'];
        $this->userid = $detailArray['userid'];
        $this->areaid = $detailArray['areaid'];
        $this->title = $detailArray['title'];
        $this->cover = $detailArray['cover'];
        $this->amount = $detailArray['amount'];
        $this->itemid = $detailArray['itemid'];
        $this->itemtype = $detailArray['itemtype'];
        $this->quantity = $detailArray['quantity'];
        $this->payment = $detailArray['payment'];
        $this->paymentid = $detailArray['paymentid'];
        $this->promoterid = $detailArray['promoterid'];
        $this->freeorder = $detailArray['freeorder'];
        $this->details = $detailArray['details'];
        $this->status = $detailArray['status'];
        $this->writeoff = $detailArray['writeoff'];
        $this->ticket = $detailArray['ticket'];
        $this->idnumber = $detailArray['idnumber'];
        $this->expire = $detailArray['expire'];
        $this->refundexpire = $detailArray['refundexpire'];
        $this->refundrequesttime = $detailArray['refundrequesttime'];
        $this->refundmessage = $detailArray['refundmessage'];
        $this->callback = $detailArray['callback'];
        $this->refundcallback = $detailArray['refundcallback'];
        $this->breachcallback = $detailArray['breachcallback'];

        $this->instantiated = true;
        return $this->success();
    }

    /**
     * 使用订单号
     * beforeAdd
     * @param  array  $data
     */
    public function beforeAdd(array &$data)
    {
        $data[static::$primaryid] =  'O_'.date("ymdHi").Encrypt::radomNum(4);
    }


    /**
     * 通过票号快速获取订单详情（取票码功能）
     * Get order detail by ticket number
     * @param  string      $ticket
     * @param  array|null  $moreFilters
     * @param  bool        $publicMode
     * @return ASResult
     */
    public function getOrderByTicket( string $ticket , array $moreFilters = null, bool $publicMode = false ): ASResult
    {

        $ticketValidDuration = getConfig('ORDER_TICKET_VALID_DURATION') ?? 3600*24*60;
        $t = time()- $ticketValidDuration ;

        $moreFilters = $moreFilters ?? [];
        $moreFilters['ticket'] = $ticket;
        $moreFilters['createtime'] = "[[>]]{$t}";
        $moreFilters['status']     = $moreFilters['status'] ?? 'pending';

        $getList = $this->list( [ 'ticket'=>$ticket ],1,1,'createtime DESC' );

        if( !$getList->isSucceed() ){ return $this->error(400,i18n('ORD_TGET_NON'),'CommerceOrder->getOrderByTicket'); }

        return $this->detail( $getList->getContent()[0]['uid'], $publicMode );
    }

    /**
     * 通过票号快速获取订单详情 公开数据模式
     * getOrderPublicByTicket
     * @param  string      $ticket
     * @param  array|null  $moreFilters
     * @return ASResult
     */
    public function getOrderPublicByTicket( string $ticket, array $moreFilters = null ): ASResult
    {
        return $this->getOrderByTicket($ticket,$moreFilters,true);
    }


    /**
     * 通过身份证获取订单
     * orderListByIdNumber
     * @param  string      $idNumber
     * @param  array|null  $moreFilters
     * @param  int         $page
     * @param  int         $size
     * @param  string      $sort
     * @param  bool        $publicMode
     * @return ASResult
     */
    public function orderListByIdNumber( string $idNumber, array $moreFilters = null, int $page = 1, int $size = 15, string $sort = 'createtime DESC', bool $publicMode = false ): ASResult
    {

        $ticketValidDuration = getConfig('ORDER_TICKET_VALID_DURATION') ?? 0;
        $t = $ticketValidDuration>0 ? time()- $ticketValidDuration :0;

        $moreFilters = $moreFilters ?? [];
        $moreFilters['idnumber'] = $idNumber;
        $moreFilters['createtime'] = "[[>]]{$t}";
        $moreFilters['status']     = $moreFilters['status'] ?? 'pending';

        return $this->list( $moreFilters,$page,$size,$sort,$publicMode );
    }

    /**
     * 通过身份证获取订单 公开数据模式
     * orderPublicListByIdNumber
     * @param  string      $idNumber
     * @param  array|null  $moreFilters
     * @param  int         $page
     * @param  int         $size
     * @param  string      $sort
     * @return ASResult
     */
    public function orderPublicListByIdNumber(string $idNumber, array $moreFilters = null, int $page = 1, int $size = 15, string $sort = 'createtime DESC'): ASResult
    {
        return $this->orderListByIdNumber( $idNumber,$moreFilters,$page,$size,$sort,true );
    }


    /**
     * 核销订单
     * writeOff
     * @param  string  $uid
     * @return ASResult
     */
    public function writeOff( string $uid ): ASResult
    {

        $call = $this->successCall( $uid );

        if( !$call->isSucceed() ){
            return $this->error(788,i18n('SYS_CALL_FAL'));
        }

        $this->update(['writeoff'=>1,'status'=>'done'],$uid);

        $this->record('ORDER_WRITEOFF','CommerceOrder->writeOff',$uid);

        return $this->take($uid)->success(i18n('ORD_WOF_SUC'),'CommerceOrder->writeOff');
    }


    /**
     * 订单列表 合并用户信息
     * listWithUserInfo
     * @param               $params
     * @param  int          $page
     * @param  int          $size
     * @param  string|NULL  $sort
     * @return ASResult
     */
    public function listWithUserInfo( $params, int $page = 1, int $size=20, string $sort = null ): ASResult
    {

        return $this->joinList( $params, null, [JoinParams::init('APS\UserInfo')->at('userid')->equalTo('commerce_order.userid')->asSubData('user')], $page , $size , $sort);

    }


    //// 订单回调

    /**
     * 订单回调
     * Run callback api
     * @param  string  $uid
     * @param  string  $mode
     * @return ASResult
     */
    public function callback( string $uid, string $mode = 'callback' ): ASResult
    {

        $callbacks = $this->detail($uid)->getContent()[$mode];

        if( isset($callbacks) ){ return $this->success(i18n('ORD_CAL_SUC')); }

        $callResult = ASAPI::systemInit('system\innerCallback',$callbacks)->run();

        if( $callResult->isSucceed() ){
            $this->clearCall( $uid,$mode );
            return $this->success();
        }else{
            $this->update( [$mode=>$callResult->getContent()] , $uid );
            return $callResult;
        }
    }


    // 成功回调
    public function successCall( string $uid ):ASResult{ return $this->callback($uid,'callback'); }

    // 退款回调
    public function refundCall( string $uid ):ASResult{ return $this->callback($uid,'refundcallback'); }

    // 违约回调
    public function breachCall( string $uid ):ASResult{ return $this->callback($uid,'breachCallback'); }

    // 清空回调
    public function clearCall( string $uid, string $mode = 'ALL' ):ASResult{

        $data = $mode=='ALL' ? ['callback'=>'SET_NULL','refundcallback'=>'SET_NULL','breachcallback'=>'SET_NULL'] : [$mode=>'SET_NULL'];
        $data['status'] = 'done';

        return $this->update($data,$uid);
    }



/* 支付部分 */

    /**
     * 订单支付成功回调
     * payback order
     * @param  string  $uid
     * @return ASResult
     */
    public function payback( string $uid ): ASResult
    {

        $this->initDetail($uid);

        # 检查订单状态是否needpay
        if ($this->status == 'needcall') {

            $callback = ASAPI::systemInit('system\innerCallback',$this->callback)->run();

            if (!$callback->isSucceed()) {

                return $this->take($uid)->error(305,i18n('SYS_CAL_SUC'),'CommerceOrder->payback');
            }

            _ASRecord()->add([
                'status'  => $callback->getStatus(),
                'itemid'  => $uid,
                'type'    => 'order',
                'event'   => 'ORDER_PAYBACK',
                'sign'    => 'CommerceOrder->payback:callback()'
            ]);

            return $this->done($uid);

        }else{

            return $this->take($uid)->success( i18n('SYS_CAL_SEC'),'CommerceOrder->payback');
        }
    }


    /**
     * 检查支付金额是否正确
     * Check amount is right
     * @param  string  $uid
     * @param  float   $paymentAmount
     * @return bool
     */
    public function amountCheck( string $uid, float $paymentAmount ): bool
    {

        $this->initDetail($uid);

        return $this->amount <= $paymentAmount;

    }


    ////////// 退款部分

    // 退款回调

    public function refund( string $uid ): ASResult
    {

        $this->initDetail($uid);

        if( time() > $this->refundexpire ){ return $this->take($uid)->error(650,i18n('ORD_REF_AEX'),'CommerceOrder->refund'); }

        $REFUND = ASAPI::systemInit('system\innerCallback',$this->refundcallback)->run();

        if( !$REFUND->isSucceed() ){ return $REFUND; }

        return $this->cancel($uid);

    }


    // 开始退款

    public function beginRefund( string $uid ): ASResult
    {

        if ( !$this->canRefund($uid) ) { return $this->take($uid)->error(650,i18n('ORD_REF_AEX'),'CommerceOrder->beginRefund'); }

        $data = ['status'=>'refunding'];
        return $this->update($data,$uid);
    }



    /////////// 查询部分


    // 是否推广订单
    public function isPromoted( string $uid ): bool
    {

        return $this->count(['uid'=>$uid,'promoterid'=>'[[NOT]]NULL'])->getContent()>0;

    }


    ////////// 订单状态部分 /////////


    // 订单状态检查

    // 订单是否待支付
    public function isNeedpay( string $uid ):ASResult{ return $this->isStatus($uid,'needpay'); }

    // 订单是否完成
    public function isDone( string $uid ):ASResult{ return $this->isStatus($uid,'done'); }

    // 订单是否付款
    public function isPaid( string $uid ):ASResult{ return $this->isStatus($uid,'paid'); }

    // 订单是否退款中
    public function isRefunding( string $uid ):ASResult{ return $this->isStatus($uid,'paid'); }

    // 检查是否可以
    public function isNeedcall( string $uid ):ASResult{ return $this->isStatus($uid,'needcall'); }

    // 订单是否过期
    public function isExpired( string $uid ): bool
    {

        $expire = (int)$this->get('expire',$uid)->getContent();

        return $expire<time() && $this->isNeedpay($uid);

    }

    // 是否可以退款

    public function canRefund( string $uid ): bool
    {

        $this->initDetail($uid);

        return $this->refundexpire > $this->refundrequesttime && $this->refundrequesttime>0;
    }

    // 订单状态更新

    // 已支付
    public function paid( string $uid ):ASResult{ return $this->status($uid,'paid'); }

    // 取消
    public function cancel( string $uid ):ASResult{ return $this->status($uid,'canceled'); }

    // 退款完成
    public function refunded( string $uid ):ASResult{ return $this->status($uid,'refunded'); }



    // 处理过期订单

    public function clearExpire( string $userid=NULL ): ASResult
    {

        $time        = time()-(3600*24*7);

        $data        = ['status'=>'expired'];

        $conditions  = " status='pending' AND expire<$time ";
        $conditions .= $userid ? " AND userid='$userid' " : "" ;

        $progress = $this->getDB()->update($data,static::$table,$conditions);
        $progress->isSucceed() ?  :
        _ASRecord()->add([
            'type'     => 'order',
            'status'   => $progress->isSucceed() ? 0 : 5500,
            'content'  => $userid,
            'event'    => 'CLEAR_EXPIRE_ORDER',
            'sign'     => 'ORDER::clearExpire',
        ]);

        return $progress->isSucceed() ?
                $this->take($userid)->success(i18n('SYS_PRG_SUC'),'CommerceOrder->clearExpire') :
                $this->error(5500,i18n('SYS_PRG_FAL'),'CommerceOrder->clearExpire');
    }


    // //  ANALYSIS  // //
    /**  统计部分  **/


    // 订单总量
    public function totalOrder( int $starttime = 0, int $endtime = 9999999999 ): ASResult
    {

        $conditions = "createtime>={$starttime} AND createtime<={$endtime} ";

        return $this->getDB()->count(static::$table,$conditions);

    }

    // 订单总量
    public function totalAmount( int $starttime = 0, int $endtime = 9999999999 ): ASResult
    {

        $conditions = "createtime>={$starttime} AND createtime<={$endtime} ";

        return $this->getDB()->sum(' amount * quantity ',static::$table,$conditions);

    }

    // 已核销订单总量
    public function totalWriteoff( int $starttime = 0, int $endtime = 9999999999 ): ASResult
    {

        $conditions = "createtime>={$starttime} AND createtime<={$endtime} ";

        return $this->getDB()->count(static::$table,$conditions." AND writeoff=1");

    }

    // 已取消订单总量
    public function totalCancel( int $starttime = 0, int $endtime = 9999999999 ): ASResult
    {

        $conditions = "createtime>={$starttime} AND createtime<={$endtime} ";

        return $this->getDB()->count(static::$table,$conditions." AND status='canceled'");

    }

    // 已过期订单总量
    public function totalExpire( int $starttime = 0, int $endtime = 9999999999 ): ASResult
    {

        $conditions = "createtime>={$starttime} AND createtime<={$endtime} ";

        return $this->getDB()->count(static::$table,$conditions." AND status='expired'");

    }

    // 订单总量
    public function totalAmountOrder( int $starttime = 0, int $endtime = 9999999999 ): ASResult
    {

        $conditions = "createtime>={$starttime} AND createtime<={$endtime} ";

        return $this->getDB()->sum(' amount*quantity ',static::$table,$conditions);

    }

    // 已核销订单总量
    public function totalAmountWriteoff( int $starttime = 0, int $endtime = 9999999999 ): ASResult
    {

        $conditions = "createtime>={$starttime} AND createtime<={$endtime} ";

        return $this->getDB()->sum(' amount*quantity ',static::$table,$conditions." AND writeoff=1");

    }

    // 已取消订单总量
    public function totalAmountCancel( int $starttime = 0, int $endtime = 9999999999 ): ASResult
    {

        $conditions = "createtime>={$starttime} AND createtime<={$endtime} ";

        return $this->getDB()->sum(' amount*quantity ',static::$table,$conditions." AND status='canceled'");

    }

    // 已过期订单总量
    public function totalAmountExpire( int $starttime = 0, int $endtime = 9999999999 ): ASResult
    {

        $conditions = "createtime>={$starttime} AND createtime<={$endtime} ";

        return $this->getDB()->sum(' amount*quantity ',static::$table,$conditions." AND status='expired'");

    }



    public function totalAnalysis( int $starttime = 0 , int $endtime = 9999999999 , int $duration = 86400 , string $amountMode = 'count' ): ASResult
    {

        $time  = new Time();
        if ($duration>=86400){ $format = 'Y-m-d'; }else if($duration>=3600){ $format = 'm-d H:00'; }else{ $format = 'm-d H:i'; }

        $loops = (int)($endtime-$starttime)/$duration ;

        $analysis = [];

        for ($i=$loops; $i >=0 ; $i--) {
            $t = new Time($endtime-$i*$duration);
            $_ = [
                'time'=>$t->time,
                'time_'=>$t->customOutput($format),
            ];
            switch ($amountMode){
                case 'amount':
                    $_['sum']   = $this->totalAmountOrder($t->time,$t->time+$duration)->getContent()['sum'];
                break;
                case 'writeOff':
                    $_['count'] = $this->totalWriteoff($t->time,$t->time+$duration)->getContent();
                break;
                case 'count':
                default:
                    $_['count'] = $this->totalOrder($t->time,$t->time+$duration)->getContent();
                break;
            }
            $analysis[] = $_;
        }

        return $this->take($analysis)->success(i18n('SYS_ANA_SUC'),'CommerceOrder->Analysis');
    }


    public function totalAmountAnalysis( int $starttime = 0 , int $endtime = 9999999999 , int $duration = 86400 ):ASResult{

        return $this->totalAnalysis($starttime,$endtime,$duration,'amount');
    }


    public function writeoffAnalysis( int $starttime = 0 , int $endtime = 9999999999 , int $duration = 86400 ):ASResult{

        return $this->totalAnalysis($starttime,$endtime,$duration,'writeOff');
    }


    public function originAnalysis( int $starttime = 0 , int $endtime = 9999999999 , int $duration = 86400 ):ASResult{

        $conditions = "createtime>=$starttime AND createtime<=$endtime";
        $count = $this->getDB()->sumByGroup('itemtype',static::$table,['amount','quantity'],'itemtype',$conditions);

        if (!$count->isSucceed()) { return $count; }

        $r = $count->getContent();
        for ($i=0; $i < count($r); $i++) {
            $r[$i]['itemtype_'] = i18n($r[$i]['itemtype']);
        }

        return $this->take($r)->success(i18n('SYS_ANA_SUC'),'CommerceOrder->totalIncome');
    }






    public static $table     = "commerce_order";  // 表
    public static $primaryid = "uid";     // 主字段
    public static $addFields = [
        'userid','areaid','itemid','itemtype',
        'title','cover',
        'amount','quantity','payment','paymentid','promoterid','freeorder',
        'details',
        'status',
        'writeoff',
        'ticket','idnumber',
        'expire','refundexpire','refundrequesttime','refundmessage',
        'callback','refundcallback',
    ];      // 添加支持字段
    public static $updateFields = [
        'title','amount','quantity',
        'payment','paymentid',
        'expire','callback','refundcallback','refundexpire',
        'status',
    ];   // 更新支持字段
    public static $detailFields = "*";   // 详情支持字段
    public static $publicDetailFields = [
        'userid','areaid','itemid','itemtype','uid',
        'title','cover',
        'amount','quantity','payment','paymentid','promoterid','freeorder',
        'details',
        'status',
        'writeoff',
        'ticket','idnumber',
        'expire','refundexpire','refundrequesttime','refundmessage',
        'createtime',
        'lasttime',
    ]; // 概览支持字段
    public static $overviewFields = [
        'userid','areaid','itemid','itemtype',
        'title','cover',
        'amount','quantity','payment','paymentid','promoterid','freeorder',
        'details',
        'status',
        'writeoff',
        'ticket','idnumber',
        'expire','refundexpire','refundrequesttime','refundmessage',
        'createtime',
        'lasttime',
    ]; // 概览支持字段
    public static $listFields = [
        'userid','areaid','itemid','itemtype','uid',
        'title','cover',
        'amount','quantity','payment','paymentid','promoterid','freeorder',
        'details',
        'status',
        'writeoff',
        'ticket','idnumber',
        'expire','refundexpire','refundrequesttime','refundmessage',
        'createtime',
        'lasttime',
    ];     // 列表支持字段
    public static $publicListFields = [
        'userid','areaid','itemid','itemtype','uid',
        'title','cover',
        'amount','quantity','payment','paymentid','promoterid','freeorder',
        'details',
        'status',
        'writeoff',
        'ticket','idnumber',
        'expire','refundexpire','refundrequesttime','refundmessage',
        'createtime',
        'lasttime',
    ];     // 开放接口列表支持字段
    public static $countFilters = [
        'userid','areaid','itemid','itemtype','uid',
        'title','cover',
        'amount','quantity','payment','paymentid','promoterid','freeorder',
        'status','writeoff','ticket','idnumber','details',
        'expire','refundexpire',
        'createtime',
        'lasttime',
    ];
    public static $depthStruct = [
        'quantity'=>'int',
        'expire'=>'int',
        'refundexpire'=>'int',
        'refundrequesttime'=>'int',
        'writeoff'=>'int',
        'freeorder'=>'int',
        'details'=>'ASJson',
        'amount'=>'float',
        'createtime'=>'int',
        'lasttime'=>'int',
    ];

}