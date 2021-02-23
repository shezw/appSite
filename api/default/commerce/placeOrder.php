<?php
/**
 * Description
 * addItem.php
 */

namespace commerce;

use APS\ASAPI;
use APS\ASModel;
use APS\ASResult;
use APS\CommerceCoupon;
use APS\CommerceOrder;
use APS\CommerceProduct;
use APS\CommerceShipping;
use APS\User;
use APS\UserAddress;

class placeOrder extends ASAPI
{

    protected $scope = 'public';
    public    $mode = 'JSON';

    private function addEmailAddress( array $address, string $userid ){

        if( UserAddress::common()->count(['userid'=>$userid])->getContent() == 0 ){
            $address['type'] = 'shipping';
            $address['userid'] = $userid;
            $addAddress = UserAddress::common()->add($address);
        }
    }

    public function run(): ASResult
    {

        // 本接口仅作为订单添加即可, 前台收到成功反馈后直接跳转支付

        // items [...]
        // coupon {couponid:''}
        // shippingAddress {}
        // billingAddress {}
        // payment ''
        // delivery {deliveryid:''}
        // autoRegist bool
        // agreeReturnPolicy bool

        $order = [ 'details'=>$this->params ];

        if( $this->user->isVerified() ){
            $order['userid'] = $this->user->userid;
            $this->addEmailAddress($this->params['shippingAddress'],$this->user->userid);
        }

        // 订单金额
        foreach ( $this->params['items'] as $i => $item ){

            $detail = CommerceProduct::common()->detail($item['productid'])->getContent();

            if( $detail['stock'] <= 0 || $detail['status'] != 'enabled' || $detail['stock'] < $item['count'] ){
                return $this->error(5,"Sorry, {$detail['title']} is out of stock for now.");
            }

            $order['title'] = $detail['title'];
            $order['cover'] = $detail['cover'];

            $order['quantity'] += $item['count'];
            $order['amount'] += $detail['sale'] * $item['count'];
        }

        // 物流费用
        if( $this->params['delivery'] ){

            $shipping = CommerceShipping::common()->detail( $this->params['delivery']['deliveryid'] );
            if( !$shipping->isSucceed() || $shipping->getContent()['status'] != 'enabled' ){
                return $this->error(6,'Delivery method is not valid');
            }
            $shipping = $shipping->getContent();
            $order['amount'] += $shipping['amount'];

        }else{
            return $this->error(-10,"You haven't choose a delivery method.");
        }

        // 优惠券
        if( $this->params['coupon'] ){
            $coupon = CommerceCoupon::common()->detail( $this->params['coupon']['couponid'] );
            if( !$coupon->isSucceed() || $coupon->getContent()['status'] != 'enabled' ){
                return $this->error(6,'Promo Code is not valid');
            }
            $coupon = $coupon->getContent();
            if( $coupon['max'] > 0 && $coupon['max'] < $order['amount'] ){
                return $this->error(10,"Promo code can only be used for orders under \${$coupon['max']}.");
            }
            if( $coupon['min'] > 0 && $coupon['min'] > $order['amount'] ){
                return $this->error(10,"Promo code can only be used for orders over \${$coupon['max']}.");
            }
            $order['amount'] -= $coupon['amount'];
            $order['couponid'] = $coupon['couponid'];

            CommerceCoupon::common()->status($order['couponid'],'used');
        }

        $order['payment'] = $this->params['payment'];

        $addOrder = CommerceOrder::common()->add($order);

        if( !$addOrder->isSucceed() ){

            return $addOrder;
        }

        // 下单成功 扣除库存
        foreach ( $this->params['items'] as $i => $item ) {

            CommerceProduct::common()->decrease('stock',['productid'=>$item['productid']],$item['count']);
        }

        CommerceOrder::common()->status($addOrder->getContent(), 'needpay' );

        if( $this->params['autoRegist'] && $this->params['autoRegist'] != 'false' ) {

            $addUser = User::common()->add([
                'email'=> $order['details']['shippingAddress']['email'],
                'nickname'=> $order['details']['shippingAddress']['firstname'] . ' ' .$order['details']['shippingAddress']['lastname']
            ]);

            $authorize = User::common()->systemAuthorize($addUser->getContent())->getContent();
            $user = new User($authorize['userid'],$authorize['token'],$authorize['scope']);
            $user->toSession();

            $this->addEmailAddress( $this->params['shippingAddress'], $authorize['userid'] );
        }

        // 卡片提前保存的，进行提前支付        
        if( $this->params['stripeCustomerID'] ){

            $stripePay = new \APS\Stripe();
            $stripePay->offlinePay( $addOrder->getContent(), $this->params['stripeCustomerID'],  $this->params['stripeMethod']);

        }


        return $this->take(['login'=>$authorize,'orderid'=>$addOrder->getContent(),'payment'=>$order['payment']])->success();
    }

}