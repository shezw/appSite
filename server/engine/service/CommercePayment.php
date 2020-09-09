<?php

namespace APS;

/**
 * 电商 - 支付
 * CommercePayment
 * @package APS
 */
class CommercePayment extends  ASModel{

    /**
     * 更新订单表中支付id
     * Update paymentid in order table beforeAddReturn
     * @param  \APS\ASResult  $result
     * @param  array          $data
     */
    public function beforeAddReturn(ASResult &$result, array $data)
    {
        if( $result->isSucceed() ){
            CommerceOrder::common()->update(['paymentid'=>$result->getContent()],$data['orderid']);
        }
    }


    // 支付完成
    public function done( string $paymentid ){ return $this->status($paymentid,'done'); }

    // 支付异常
    public function exception( string $paymentid ){ return $this->status($paymentid,'exception'); }

    // 支付款项不足
    public function badpay( string $paymentid ){ return $this->status($paymentid,'badpay'); }

    // 支付单是否存在 isExist()

    // 支付单是否某状态 isStatus()

    // 支付单是否待支付
    public function isNeedpay( string $paymentid ){ return $this->isStatus($paymentid,'needpay'); }

    // 支付单是否完成
    public function isDone( string $paymentid ){ return $this->isStatus($paymentid,'done'); }


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
        $addPayment  =  $this->add($paydata);

        if( !$addPayment->isSucceed() ){ return $this->error(500,'Add payment failed.'); }

        $paymentid = $addPayment->getContent();
        $orderid   = $paydata['orderid'];

        $order = CommerceOrder::instance($orderid);
        $order->status( $order->uid,'needcall' );

        $callback = $order->payback( $order->uid );

        if(!$callback->isSucceed()){

            _ASRecord()->add([
                'category'=>'system',
                'content'=>$callback,
                'status'=>12345,
                'event'=>'PAYMENT_CALLBACK',
                'sign'=>'CommercePayment->callback:orderpayback()'
            ]);

            return $this->error(12345,'业务逻辑错误','CommercePayment->callback');
        }

        // 更新状态
        $order->update(['paymentid'=>$paymentid,'status'=>'done'],$orderid);

        return $this->done($paymentid);

    }




    public static $table     = "commerce_payment";  // 表
    public static $primaryid = "uid";     // 主字段
    public static $addFields = [
        'orderid',
        'payment',
        'paymenttype',
        'paymenttradeno',
        'amount',
        'status',
        'details',
    ];      // 添加支持字段
    public static $updateFields = [
        'status',
        // 'details',
    ];   // 更新支持字段  由于支付单敏感  不开放原始数据修改
    public static $detailFields = "*";   // 详情支持字段
    public static $publicDetailFields = [
        'orderid',
        'payment',
        'paymenttype',
        'paymenttradeno',
        'amount',
        'status',
        // 'details',
    ]; // 概览支持字段
    public static $overviewFields = [
        'orderid',
        'payment',
        'paymenttype',
        'paymenttradeno',
        'amount',
        'status',
        // 'details',
    ]; // 概览支持字段
    public static $listFields = [
        'orderid',
        'payment',
        'paymenttype',
        'paymenttradeno',
        'amount',
        'status',
        // 'details',
    ];     // 列表支持字段
    public static $publicListFields = [
        'orderid',
        'payment',
        'paymenttype',
        'paymenttradeno',
        'amount',
        'status',
        // 'details',
    ];     // 开放接口列表支持字段
    public static $countFilters = [
        'orderid',
        'payment',
        'paymenttype',
        'paymenttradeno',
        'amount',
        'status',
    ];
    public static $depthStruct = [
        'amount'=>'int',
        'details'=>'ASJson',
    ];

}