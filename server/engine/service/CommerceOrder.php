<?php

namespace APS;

use innerCallback;

/**
 * 电商 - 订单
 * CommerceOrder
 *
 *
 * 订单-支付流程描述:
 * 订单 ORDER
    * 支付 PAYMENT
 *
* 0 查询订单
    * 0.1 检测订单状态 ( pending 等待中 needpay 待支付  needcall 待回调  expired 已过期  done 已完成  [ 暂不考虑物流 如考虑需要加入 运输中等状态 ] 或增加物流表 )
    * 0.1.1 待支付 -> 2
    * 0.1.2 待回调 -> 3
    * 0.1.3 已过期 -> 4.3
 *
* 1 生成订单表单
 *
* 2 生成支付表单
    * 2.0 检测表单状态
    * 2.0.1 未过期,待支付 -> 2.1
    * 2.0.2 已过期 -> 5.3 -> 2
    * 2.1 请求支付接口
    * 2.1.1 请求完成,等待支付接口回调
    * 2.1.2 请求失败(由支付接口发起失败) -> 5.2
 *
* 2.2.0 未回调
    * 2.2.1 回调支付完成 -> 5.1 -> 3
    * 2.2.2 回调支付失败(由系统内部发起失败) -> 5.2
 *
* 3 回调订单表单
    * 3.1 检测订单状态
    * 3.1.1 待回调,进行回调
    * 3.1.2 无需回调,系统标记 (+错误日志)
    * 3.2 执行回调
    * 3.2.1 订单回调完成,更新订单状态
    * 3.2.2 订单回调失败,标记订单状态 (+错误日志)
 *
* 4 更新订单状态
    * 4.1 完成
    * 4.2 过期
    * 4.3 核销
 *
* 5 更新支付状态
    * 5.1 完成
    * 5.2 失败
    * 5.3 过期
 *
 * @package APS\service\Commerce
 */
class CommerceOrder extends ASModel{

    const table     = "commerce_order";
    const comment   = '电商-订单';
    const primaryid = "uid";
    const addFields = [
        'uid','saasid','userid','areaid','itemid','itemtype',
        'title','cover',
        'amount','quantity','payment','paymentid','promoterid','freeorder',
        'details',
        'status',
        'writeoff',
        'ticket','idnumber',
        'expire','refundexpire','refundrequesttime','refundmessage',
        'callback','refundcallback',
    ];
    const updateFields = [
        'title','amount','quantity',
        'payment','paymentid',
        'expire','callback','refundcallback','refundexpire',
        'status',
    ];
    const detailFields = [
        'uid','saasid','userid','areaid','itemid','itemtype',
        'title','cover',
        'amount','quantity','payment','paymentid','promoterid','freeorder',
        'details',
        'status',
        'writeoff',
        'ticket','idnumber',
        'expire','refundexpire','refundrequesttime','refundmessage',
        'callback','refundcallback',
        'createtime','lasttime',
    ];
    const publicDetailFields = [
        'userid','saasid','areaid','itemid','itemtype','uid',
        'title','cover',
        'amount','quantity','payment','paymentid','promoterid','freeorder',
        'details',
        'status',
        'writeoff',
        'ticket','idnumber',
        'expire','refundexpire','refundrequesttime','refundmessage',
        'createtime','lasttime',
    ];
    const overviewFields = [
        'userid','saasid','areaid','itemid','itemtype',
        'title','cover',
        'amount','quantity','payment','paymentid','promoterid','freeorder',
        'details',
        'status',
        'writeoff',
        'ticket','idnumber',
        'expire','refundexpire','refundrequesttime','refundmessage',
        'createtime',
        'lasttime',
    ];
    const listFields = [
        'userid','saasid','areaid','itemid','itemtype','uid',
        'title','cover',
        'amount','quantity','payment','paymentid','promoterid','freeorder',
        'details',
        'status',
        'writeoff',
        'ticket','idnumber',
        'expire','refundexpire','refundrequesttime','refundmessage',
        'createtime',
        'lasttime',
    ];
    const publicListFields = [
        'userid','saasid','areaid','itemid','itemtype','uid',
        'title','cover',
        'amount','quantity','payment','paymentid','promoterid','freeorder',
        'details',
        'status',
        'writeoff',
        'ticket','idnumber',
        'expire','refundexpire','refundrequesttime','refundmessage',
        'createtime',
        'lasttime',
    ];
    const filterFields = [
        'userid','saasid','areaid','itemid','itemtype','uid',
        'title','cover',
        'amount','quantity','payment','paymentid','promoterid','freeorder',
        'status','writeoff','ticket','idnumber',
        'expire','refundexpire',
        'createtime',
        'lasttime',
    ];
    const depthStruct = [
        'quantity'=>DBField_Int,
        'expire'=>DBField_TimeStamp,
        'refundexpire'=>DBField_TimeStamp,
        'refundrequesttime'=>DBField_TimeStamp,
        'writeoff'=>DBField_Boolean,
        'freeorder'=>DBField_Boolean,
        'details'=>DBField_Json,
        'amount'=>DBField_Decimal,
        'createtime'=>DBField_TimeStamp,
        'lasttime'=>DBField_TimeStamp,
    ];

    const tableStruct = [

        'uid'=>         ['type'=>DBField_String,    'len'=>32,  'nullable'=>0,  'cmt'=>'订单ID' , 'idx'=>DBIndex_Unique ],
        'saasid'=>      ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'所属saas',  'idx'=>DBIndex_Index,],
        'userid'=>      ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'用户ID' , 'idx'=>DBIndex_Index ],
        'areaid'=>      ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'地区ID' , 'idx'=>DBIndex_Index ],

        'title'=>       ['type'=>DBField_String,    'len'=>127, 'nullable'=>0,  'cmt'=>'标题 60字以内 分词' ,  'idx'=>DBIndex_FullText ],
        'cover'=>       ['type'=>DBField_String,    'len'=>255, 'nullable'=>1,  'cmt'=>'缩略图 小图' ],

        'amount'=>      ['type'=>DBField_Decimal,   'len'=>'12,2',  'nullable'=>0,  'cmt'=>'总价 精度0.01元' ,   'dft'=>0,       ],
        'itemid'=>      ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'商品ID' , 'idx'=>DBIndex_Index ],
        'itemtype'=>    ['type'=>DBField_String,    'len'=>32,  'nullable'=>0,  'cmt'=>'商品类型' ],
        'quantity'=>    ['type'=>DBField_Int,       'len'=>8,   'nullable'=>0,  'cmt'=>'数量',    'dft'=>1,       ],

        'payment'=>     ['type'=>DBField_String,    'len'=>64,  'nullable'=>1,  'cmt'=>'支付方式 如 wechat-jsapi' ],
        'paymentid'=>   ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'支付方式 如 wechat-jsapi' ],
        'promoterid'=>  ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'推广人ID ' ,       'idx'=>DBIndex_Index ],
        'freeorder'=>   ['type'=>DBField_Int,       'len'=>1,   'nullable'=>0,  'cmt'=>'免支付订单' ,        'dft'=>0,       ],

        'details'=>     ['type'=>DBField_Json,      'len'=>-1,  'nullable'=>1,  'cmt'=>'详情记录' ],
        'status'=>      ['type'=>DBField_String,    'len'=>12,  'nullable'=>0,  'cmt'=>'状态',    'dft'=>'needpay',       ],
        // eg: needpay待支付, pending等待中, shipping物流中, precancel取消中, needconfirm待审核, done已完成, error错误, closed已关闭, canceled已取消
        'writeoff'=>    ['type'=>DBField_Int,       'len'=>1,   'nullable'=>0,  'cmt'=>'是否核销' , 'dft'=>0,       ],

        'ticket'=>      ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'票号 // 取票码' ],
        'idnumber'=>    ['type'=>DBField_String,    'len'=>32,  'nullable'=>1,  'cmt'=>'身份证取票 // 取票码' , 'idx'=>DBIndex_Index ],

        'expire'=>      ['type'=>DBField_Int,       'len'=>11,  'nullable'=>0,  'cmt'=>'过期时间 时间戳' ,     'dft'=>0,       ],

        'refundexpire'=>  ['type'=>DBField_Int,       'len'=>11,  'nullable'=>0,  'cmt'=>'退款有效期' ,        'dft'=>0,       ],
        'refundrequesttime'=>['type'=>DBField_Int,       'len'=>11,  'nullable'=>0,  'cmt'=>'退款申请日期' ,       'dft'=>0,       ],
        'refundmessage'=> ['type'=>DBField_String,    'len'=>511, 'nullable'=>1,  'cmt'=>'退款申请留言 250字以内' ],

        'callback'=>      ['type'=>DBField_Json,      'len'=>-1,  'nullable'=>1,  'cmt'=>'订单成功回调 k-v json' ],
        'refundcallback'=>['type'=>DBField_Json,      'len'=>-1,  'nullable'=>1,  'cmt'=>'退款回调' ],
        'breachcallback'=>['type'=>DBField_Json,      'len'=>-1,  'nullable'=>1,  'cmt'=>'惩罚回调' ],
        // 回调函数最外层必须是 queue数组 [{},{},{},{}] or [{}]
        // eg: {{'action':'setvip','params':{'userid':'xxx','expire':158989990}},{'action':'newPromotion','params':{'userid':'xxx','amount':8.88}}
        // 通过回调函数激活支付成功后进行的事件

        'createtime'   =>['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'创建时间',        'idx'=>DBIndex_Index, ],
        'lasttime'     =>['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'上一次更新时间', ],
    ];


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
     * @var mixed
     */
    private $saasid;

    /**
     * 实例化
     * instance
     * @param $uid
     * @return CommerceOrder
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
        $this->saasid = $detailArray['saasid'];
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
     * @param DBValues $data
     */
    public function beforeAdd( DBValues &$data)
    {
        $data->set(static::primaryid)->string('O_'.date("ymdHi").Encrypt::randomNumber(4));
    }


    /**
     * 通过票号快速获取订单详情（取票码功能）
     * Get order detail by ticket number
     * @param string $ticket
     * @param DBConditions|null $moreFilters
     * @param bool $publicMode
     * @return ASResult
     */
    public function getOrderByTicket( string $ticket , DBConditions $moreFilters = null, bool $publicMode = false ): ASResult
    {
        $ticketValidDuration = getConfig('ORDER_TICKET_VALID_DURATION') ?? 3600*24*60;

        $filters = $moreFilters ?? DBConditions::init();
        $filters->where('ticket')->equal($ticket)
            ->and('createtime')->bigger(time()- $ticketValidDuration )
            ->and('status')->equal( 'pending' )
            ->and('saasid')->equalIf(saasId());

        $getList = $this->list(  $filters,1,1,'createtime DESC' );

        if( !$getList->isSucceed() ){ return $this->error(400,i18n('ORD_TGET_NON'),'CommerceOrder->getOrderByTicket'); }

        return $this->detail( $getList->getContent()[0]['uid'], $publicMode );
    }

    /**
     * 通过票号快速获取订单详情 公开数据模式
     * getOrderPublicByTicket
     * @param string $ticket
     * @param DBConditions|null $moreFilters
     * @return ASResult
     */
    public function getOrderPublicByTicket( string $ticket, DBConditions $moreFilters = null ): ASResult
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

        $filters = $moreFilters ?? DBConditions::init();
        $filters->where('idnumber')->equal($idNumber)
            ->and('createtime')->bigger(time()- $ticketValidDuration )
            ->and('status')->equal( 'pending' )
            ->and('saasid')->equalIf(saasId());

        return $this->list( $filters,$page,$size,$sort,$publicMode );
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

        $this->update(DBValues::init('writeoff')->bool(true)->set('status')->string('done'),$uid);

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
//    public function listWithUserInfo( $params, int $page = 1, int $size=20, string $sort = null ): ASResult
//    {
//        return $this->joinList( $params, null, [JoinParams::init(UserInfo::class)->at('userid')->equalTo('commerce_order.userid')->asSubData('user')], $page , $size , $sort);
//    }


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

        $callResult = ASAPI::systemInit(innerCallback::class,$callbacks)->run();

        if( $callResult->isSucceed() ){
            $this->clearCall( $uid,$mode );
            return $this->success();
        }else{
            $this->update( DBValues::init($mode)->string($callResult->getContent()) , $uid );
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

        $data = DBValues::init('status')->string('done');
        if( $mode == 'ALL' ){
            $data->set('callback')->null()->set('refundcallback')->null()->set('breachcallback')->null();
        }else{
            $data->set($mode)->null();
        }

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

            $callback = ASAPI::systemInit(innerCallback::class,$this->callback)->run();

            if (!$callback->isSucceed()) {

                return $this->take($uid)->error(305,i18n('SYS_CAL_SUC'),'CommerceOrder->payback');
            }

            _ASRecord()->save([
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

        $REFUND = ASAPI::systemInit(innerCallback::class,$this->refundcallback)->run();

        if( !$REFUND->isSucceed() ){ return $REFUND; }

        return $this->cancel($uid);

    }


    // 开始退款

    public function beginRefund( string $uid ): ASResult
    {

        if ( !$this->canRefund($uid) ) { return $this->take($uid)->error(650,i18n('ORD_REF_AEX'),'CommerceOrder->beginRefund'); }

        return $this->update(DBValues::init('status')->string('refunding'),$uid);
    }



    /////////// 查询部分


    // 是否推广订单
    public function isPromoted( string $uid ): bool
    {
        return $this->count(
            DBConditions::init()
                ->where(static::primaryid)->equal($uid)
                ->and('promoterid')->isNotNull()
            )->getContent()>0;
    }


    ////////// 订单状态部分 /////////


    // 订单状态检查

    // 订单是否待支付
    public function isNeedpay( string $uid ): bool
    { return $this->isStatus($uid,'needpay'); }

    // 订单是否完成
    public function isDone( string $uid ): bool
    { return $this->isStatus($uid,'done'); }

    // 订单是否付款
    public function isPaid( string $uid ): bool
    { return $this->isStatus($uid,'paid'); }

    // 订单是否退款中
    public function isRefunding( string $uid ): bool
    { return $this->isStatus($uid,'paid'); }

    // 检查是否可以
    public function isNeedcall( string $uid ): bool
    { return $this->isStatus($uid,'needcall'); }

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

        $data = DBValues::init('status')->string('expired');
        $conditions = DBConditions::init('status')->equal('pending')->and('expire')->less($time)->and('userid')->equalIf($userid)->and('saasid')->equalIf(saasId());

        $progress = $this->getDB()->update($data,static::table,$conditions);
        $progress->isSucceed() ?  :
        _ASRecord()->save([
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
    /**  统计部分  *
     * @param int $starttime
     * @param int $endtime
     * @return ASResult
     */

    // 订单总量
    public function totalOrder( int $starttime = 0, int $endtime = 9999999999 ): ASResult
    {
        return $this->getDB()->count(static::table,DBConditions::init()->where('createtime')->between($starttime,$endtime)->and('saasid')->equalIf(saasId()));
    }

    // 订单总量
    public function totalAmount( int $starttime = 0, int $endtime = 9999999999 ): ASResult
    {
        return $this->getDB()->get(DBFields::init()->and('amount * quantity')->as('total'),static::table,DBConditions::init()->where('createtime')->between($starttime,$endtime)->and('saasid')->equalIf(saasId()));
    }

    // 已核销订单总量
    public function totalWriteOff(int $starttime = 0, int $endtime = 9999999999 ): ASResult
    {
        $conditions = DBConditions::init()
            ->where('createtime')->between($starttime,$endtime)
            ->and('saasid')->equalIf(saasId())
            ->and('writeoff')->bool(true);

        return $this->getDB()->count(static::table,$conditions);
    }

    // 已取消订单总量
    public function totalCancel( int $starttime = 0, int $endtime = 9999999999 ): ASResult
    {
        $conditions = DBConditions::init()
            ->where('createtime')->between($starttime,$endtime)
            ->and('saasid')->equalIf(saasId())
            ->and('status')->equal('canceled');

        return $this->getDB()->count(static::table,$conditions);
    }

    // 已过期订单总量
    public function totalExpire( int $starttime = 0, int $endtime = 9999999999 ): ASResult
    {
        $conditions = DBConditions::init(static::table)
            ->where('createtime')->between($starttime,$endtime)
            ->and('saasid')->equalIf(saasId())
            ->and('status')->equal('expired');

        return $this->getDB()->count(static::table,$conditions);
    }

    // 订单总量
    public function totalAmountOrder( int $starttime = 0, int $endtime = 9999999999 ): ASResult
    {
        return $this->getDB()->get(DBFields::init()->and('amount * quantity')->sumAs('total'),static::table,DBConditions::init()->where('createtime')->between($starttime,$endtime)->and('saasid')->equalIf(saasId()));
    }

    // 已核销订单总量
    public function totalAmountWriteOff( int $starttime = 0, int $endtime = 9999999999 ): ASResult
    {
        $conditions = DBConditions::init()
            ->where('createtime')->between($starttime,$endtime)
            ->and('saasid')->equalIf(saasId())
            ->and('writeoff')->bool(true);

        return $this->getDB()->get(DBFields::init()->and('amount * quantity')->sumAs('total'),static::table,$conditions);
    }

    // 已取消订单总量
    public function totalAmountCancel( int $starttime = 0, int $endtime = 9999999999 ): ASResult
    {
        $conditions = DBConditions::init(static::table)
            ->where('createtime')->between($starttime,$endtime)
            ->and('saasid')->equalIf(saasId())
            ->and('status')->equal('canceled');

        return $this->getDB()->get(DBFields::init()->and('amount * quantity')->sumAs('total'),static::table,$conditions);
    }

    // 已过期订单总量
    public function totalAmountExpire( int $starttime = 0, int $endtime = 9999999999 ): ASResult
    {
        $conditions = DBConditions::init(static::table)
            ->where('createtime')->between($starttime,$endtime)
            ->and('saasid')->equalIf(saasId())
            ->and('status')->equal('expired');

        return $this->getDB()->get(DBFields::init()->and('amount * quantity')->sumAs('total'),static::table,$conditions);
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
                    $_['count'] = $this->totalWriteOff($t->time,$t->time+$duration)->getContent();
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


    public function originAnalysis( int $starttime = 0 , int $endtime = 9999999999 , int $duration = 86400 ):ASResult
    {
        $conditions = DBConditions::init()
            ->where('createtime')->between($starttime,$endtime)
            ->and('saasid')->equalIf(saasId())
            ->groupBy('itemtype');

        $count = $this->getDB()->get(DBFields::init()->and('amount')->sumAs('sum_amount')->and('quantity')->sumAs('sum_quantity'),static::table,$conditions);

        if (!$count->isSucceed()) { return $count; }

        $r = $count->getContent();
        for ($i=0; $i < count($r); $i++) {
            $r[$i]['itemtype_'] = i18n($r[$i]['itemtype']);
        }

        return $this->take($r)->success(i18n('SYS_ANA_SUC'),'CommerceOrder->totalIncome');
    }


}