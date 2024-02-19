<?php

namespace APS;

/**
 * 电商 - 支付
 * CommercePayment
 * @package APS
 */
class CommercePayment extends  ASModel{

    const table     = "commerce_payment";
    const comment   = "电商-支付";
    const primaryid = "uid";
    const addFields = [
        'orderid',
        'payment',
        'paymenttype',
        'paymenttradeno',
        'amount',
        'status',
        'details',
        'createtime',
        'lasttime',
    ];
    const updateFields = [ # 由于支付单敏感  不开放原始数据修改
        'status',
    ];
    const detailFields = [
        'orderid',
        'payment',
        'paymenttype',
        'paymenttradeno',
        'amount',
        'status',
        'details',
        'createtime',
        'lasttime',
    ];
    const publicDetailFields = [
        'orderid',
        'payment',
        'paymenttype',
        'paymenttradeno',
        'amount',
        'status',
        'createtime',
        'lasttime',

    ];
    const overviewFields = [
        'orderid',
        'payment',
        'paymenttype',
        'paymenttradeno',
        'amount',
        'status',
        'createtime',
        'lasttime',

    ];
    const listFields = [
        'orderid',
        'payment',
        'paymenttype',
        'paymenttradeno',
        'amount',
        'status',
        'createtime',
        'lasttime',

    ];
    const publicListFields = [
        'orderid',
        'payment',
        'paymenttype',
        'paymenttradeno',
        'amount',
        'status',
        'createtime',
        'lasttime',
        // 'details',
    ];     // 开放接口列表支持字段
    const filterFields = [
        'orderid',
        'payment',
        'paymenttype',
        'paymenttradeno',
        'amount',
        'status',
        'createtime',
        'lasttime',
    ];
    const depthStruct = [
        'amount'=>DBField_Decimal,
        'expire'=>DBField_TimeStamp,
        'details'=>DBField_Json,
        'createtime'=>DBField_TimeStamp,
        'lasttime'=>DBField_TimeStamp
    ];

    const tableStruct = [

        'uid'=>           ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'支付ID',  'idx'=>DBIndex_Unique ],
        'saasid'       => ['type'=>DBField_String,  'len'=>8,   'nullable'=>1,    'cmt'=>'所属saas',  'idx'=>DBIndex_Index,],
        'orderid'=>       ['type'=>DBField_String,    'len'=>32,  'nullable'=>1,  'cmt'=>'订单ID',  'idx'=>DBIndex_Index ],
        'payment'=>       ['type'=>DBField_String,    'len'=>16,  'nullable'=>1,  'cmt'=>'支付方式 如 wechat alipay...',  ],
        'paymenttype'=>   ['type'=>DBField_String,    'len'=>16,  'nullable'=>1,  'cmt'=>'支付方式 如 jsapi qrcode h5 web ...',  ],

        'paymenttradeno'=>['type'=>DBField_String,    'len'=>32,  'nullable'=>1,  'cmt'=>'支付流水号 ',  ],
        // 'itemid'=>     ['type'=>DBFieldType_STRING,'len'=>32,  'nullable'=>1,  'cmt'=>'商品ID',  ],

        'amount'=>        ['type'=>DBField_Decimal,   'len'=>'12,2',  'nullable'=>0,  'cmt'=>'总价 精度0.01元', 'dft'=>0,       ],

        'status'=>        ['type'=>DBField_String,    'len'=>12,  'nullable'=>0,  'cmt'=>'状态',    'dft'=>'paid',    ],
        // eg: needpay待付款, waiting待确认, done已完成, error错误, closed已关闭, canceled已取消
        'expire'=>        ['type'=>DBField_TimeStamp,    'len'=>-1,  'nullable'=>0,  'cmt'=>'支付过期时间 时间戳',  'dft'=>0,       ],

        'details'=>       ['type'=>DBField_Json,      'len'=>-1,  'nullable'=>1,  'cmt'=>'详情记录',  ],

        // 和 order callback不同 支付通过paymentid的回调 让支付可以顺利获取自身数据即可,后续操作根据 orderid获取 function callback

        'createtime'   =>['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'创建时间',                      'idx'=>DBIndex_Index, ],
        'lasttime'     =>['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'上一次更新时间', ],
    ];

    /**
     * 更新订单表中支付id
     * Update paymentid in order table beforeAddReturn
     * @param ASResult $result
     * @param DBValues $data
     */
    public function beforeAddReturn(ASResult &$result, DBValues $data)
    {
        if( $result->isSucceed() ){
            CommerceOrder::common()->update(DBValues::init('paymentid')->string($result->getContent()),$data['orderid']);
        }
    }


    // 支付完成
    public function done( string $uid ): ASResult{ return $this->status($uid,'done'); }

    // 支付异常
    public function exception( string $paymentid ): ASResult{ return $this->status($paymentid,'exception'); }

    // 支付款项不足
    public function badpay( string $paymentid ): ASResult{ return $this->status($paymentid,'badpay'); }

    // 支付单是否存在 isExist()

    // 支付单是否某状态 isStatus()

    // 支付单是否待支付
    public function isNeedpay( string $paymentid ):bool{ return $this->isStatus($paymentid,'needpay'); }

    // 支付单是否完成
    public function isDone( string $paymentid ):bool{ return $this->isStatus($paymentid,'done'); }


    // 支付完成回调
    public function callback( array $params ){

        $t = new Time();

        $details = [];
        $details['callbackTime']   = $t;
        $details['callbackTime_']  = $t->customOutput("Y-01-01 H:i:s");
        $details['paytime']        = $params['paytime']; # 用户支付时间
        $details['callbackStatus'] = $params['status'];  # 支付状态
        $details['callbackMessage']= $params['message']; # 支付方消息
        $details['others']         = $params['others'];  # 补充

        $paydata['orderid']        = $params['orderid'];        // 订单号
        $paydata['payment']        = $params['payment'];        // 支付方式
        $paydata['amount']         = (double)$params['amount']; // 支付金额
        $paydata['paymenttradeno'] = $params['paymenttradeno']; // 支付流水号
        $paydata['details']        = $details; // 详情

        /* 创建支付单 */
        $addPayment  =  $this->addByArray($paydata);

        if( !$addPayment->isSucceed() ){ return $this->error(500,'Add payment failed.'); }

        $paymentid = $addPayment->getContent();
        $orderid   = $paydata['orderid'];

        $order = CommerceOrder::instance($orderid);
        $order->status( $order->uid,'needcall' );

        $callback = $order->payback( $order->uid );

        if(!$callback->isSucceed()){

            _ASRecord()->save([
                'category'=>'system',
                'content'=>$callback,
                'status'=>12345,
                'event'=>'PAYMENT_CALLBACK',
                'sign'=>'CommercePayment->callback:orderpayback()'
            ]);

            return $this->error(12345,'业务逻辑错误','CommercePayment->callback');
        }

        // 更新状态
        $order->updateByArray(['paymentid'=>$paymentid,'status'=>'done'],$orderid);

        return $this->done($paymentid);

    }


}