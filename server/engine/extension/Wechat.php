<?php

namespace APS;

use OSS\Core\OssException;
use WeChat\Exceptions\InvalidDecryptException;
use WeChat\Exceptions\InvalidResponseException;
use WeChat\Exceptions\LocalCacheException;
use WeChat\Pay;
use WeChat\Script;
use WeMini\Crypt;
use WeOpen\Oauth;

include_once SERVER_DIR.'library/weChatDev/include.php';

/**
 * 微信封装
 * Wechat
 * @package APS\extension
 */
class Wechat extends ASObject{

    var $appID;
    var $token;
    var $appSecret;
    var $encodingAESKey;

    # 配置商户支付参数
    var $mch_id;
    var $mch_key;

    # 配置商户支付双向证书目录
    var $ssl_key;
    var $ssl_cer;

    # 配置缓存目录，需要拥有写权限
    var $cache_path;

    # 开放平台参数
    var $openKey;
    var $openSecret;

    # 小程序参数
    private $miniId;
    private $miniSecret;

    public function __construct(){

        parent::__construct();

        $this->appID           = getConfig('WXMP_ID','WECHAT');
        $this->token           = getConfig('WXMP_TOKEN','WECHAT');
        $this->appSecret       = getConfig('WXMP_SECRET','WECHAT');
        $this->encodingAESKey  = getConfig('WXMP_EncodingAESKey','WECHAT');

        # 配置商户支付参数
        $this->mch_id          = getConfig('WXPAY_ID','WECHAT');
        $this->mch_key         = getConfig('WXPAY_KEY','WECHAT');

        # 配置商户支付双向证书目录
        $this->ssl_key        = getConfig('WXPAY_SSLCERT_PATH','WECHAT').'apiclient_key.pem';
        $this->ssl_cer        = getConfig('WXPAY_SSLKEY_PATH','WECHAT').'apiclient_cert.pem';

        $this->openKey        = getConfig('WXOPEN_ID','WECHAT');
        $this->openSecret     = getConfig('WXOPEN_SECRET','WECHAT');

        $this->miniId         = getConfig('WXMINI_ID','WECHAT');
        $this->miniSecret     = getConfig('WXMINI_SECRET','WECHAT');

        # 配置缓存目录，需要拥有写权限
        $this->cache_path     = __DIR__.'/weCache/' ;

    }

    public function getConfig():array {
        return [
            'appid' => $this->appID,
            'token' => $this->token,
            'appsecret' => $this->appSecret,
            'encodingaeskey' => $this->encodingAESKey,

            # 配置商户支付参数
            'mch_id' => $this->mch_id,
            'mch_key' => $this->mch_key,

            # 配置商户支付双向证书目录
            'ssl_key' => $this->ssl_key,
            'ssl_cer' => $this->ssl_cer,

            # 配置缓存目录，需要拥有写权限
            'cache_path' => $this->cache_path,

            'openkey' => $this->openKey,
            'opensecret' => $this->openSecret,
        ];
    }

    /**
     * 通用单例
     * common
     * @return Wechat
     */
    public static function common():Wechat{
        return new Wechat();
    }

    /**
     * 获取微信用户信息
     * getInfo
     * @param          $params
     *                        code          ?string    回调前
     *                        device        ?string    回调成功
     *                        userid        ?string
     *                        callbackurl   ?string
     * @param  string  $callbackURI
     * @return ASResult | void
     */
    public function getInfo($params, string $callbackURI = 'api/account/loginByWechat' ): ASResult
    {

        if (isset($params['code'])) {
            return $this->oauthCallback($params);
        }else{
            $this->oauthRequest($params,$callbackURI);
        }

    }

    /**
     * 网页端发起授权页面跳转
     * oauthRequest by page direction
     * @param          $params
     * @param  string  $callbackURI
     */
    public function oauthRequest( $params, string $callbackURI = 'api/account/loginByWechat' ){

        $device   = isset($params['device']) ? $params['device'] : 'mobile';
        $isPc     = $device!=='mobile';
        $wechat   = $isPc ? new \WeOpen\Oauth($this->getConfig()) : new \WeChat\Oauth($this->getConfig());

        if( !strstr($params['callbackurl'], '://')){ $params['callbackurl'] = str_replace('http:/','http://',str_replace('https:/', 'https://', $params['callbackurl'])); }

        $redirect = getConfig('MAIN_PATH').$callbackURI.'?callbackurl='.$params['callbackurl'].'&device='.$device;
        $redirect.= isset($params['userid']) ? '&userid='.$params['userid'] : '';

        _ASRecord()->add(['content'=>['params'=>$params,'redirect'=>$redirect,'callbackURI'=>$callbackURI,'server'=>$_SERVER],'event'=>'Wechat->oauthRequest']);

        $oauthUrl = $wechat->getOauthRedirect($redirect,time(),$isPc?'snsapi_login':'snsapi_userinfo');

        header("Location:$oauthUrl");
        exit();
    }

    /**
     * 网页授权回调 接收用户信息
     * oauthCallback
     * @param $params
     * @return ASResult
     */
    public function oauthCallback($params): ASResult
    {

        $isPc     = isset($params['device']) && $params['device']!=='mobile';
        $wechat   = $isPc ? new Oauth($this->getConfig()) : new \WeChat\Oauth($this->getConfig());

        try {
            $result = $wechat->getOauthAccessToken();

            if (isset($result['access_token'])) {
                try {
                    $user = $wechat->getUserInfo($result['access_token'], $result['openid']);

                    _ASRecord()->add(['content'=>$user,'event'=>'WECHAT_GET_USERINFO']);

                    return $this->take($user)->success('SYS_GET_SUC','Wechat->oauthCallback');

                } catch (InvalidResponseException $e) {
                    $ex = $e;
                    $message = 'InvalidResponse Get UserInfo Failed';
                } catch (LocalCacheException $e) {
                    $ex = $e;
                    $message = 'LocalCacheException Get UserInfo Failed';
                }
            }else{
                $ex = $result;
                $message = 'Access_token not response';
            }

        } catch (InvalidResponseException $e) {
            $ex = $e;
            $message = 'InvalidResponse Get OauthAccessToken Failed';
        } catch (LocalCacheException $e) {
            $ex = $e;
            $message = 'LocalCache Get OauthAccessToken Failed';
        }
        return $this->take($ex)->error(611,$message);
    }

    /**
     * 微信小程序快速登录
     * miniProgramLogin
     * @param $params
     * @return ASResult
     */
    public function miniProgramLogin( $params ): ASResult
    {

        $config = [ 'appid'=> getConfig('WXMINI_ID','WECHAT'), 'appsecret'=> getConfig('WXMINI_SECRET','WECHAT') ];

        $mini = new Crypt($config);

        try {
            $userInfo = $mini->userInfo($params['code'], $params['iv'], $params['encryptedData']);
            return $this->oauthLogin($userInfo,'miniProgram');
        } catch (InvalidDecryptException $e) {
            $ex = $e;
        } catch (InvalidResponseException $e) {
            $ex = $e;
        } catch (LocalCacheException $e) {
            $ex = $e;
        }

        return $this->take($ex)->error( 610, 'MiniProgram login Failed' );
    }


    /**
     * 通过微信进行登陆 (自动注册)
     * Login by wechat authorization with auto regist
     * oauthLogin
     * @param          $params
     * @param  string  $scope
     * @return ASResult
     */
    public function oauthLogin( $params , $scope = 'common' ): ASResult
    {

        $tryGetUserInfo = isset($params['unionid'])||isset($params['openid']) ? $params : $this->getInfo($params);

        $userInfo = $tryGetUserInfo->getContent();
        $weChatID = $userInfo['unionid'] ?? $userInfo['openid'];

        if ( !UserInfo::common()->has(DBConditions::init()->where('wechatid')->equal($weChatID) ) ) { # 未注册

            if (isset($userInfo)) {
                $avatar = AliyunOSS::common()->uploadUrlFile($userInfo['headimgurl']??$userInfo['avatarUrl'])->getContentOr(null);
                $data = DBValues::init('avatar')->stringIf($avatar)
                    ->set('wechatid')->string($weChatID)
                    ->set('nickname')->stringIf($userInfo['nickname'] ?? $userInfo['nickName'])
                    ->set('country')->stringIf($userInfo['country'])
                    ->set('province')->stringIf($userInfo['province'])
                    ->set('city')->stringIf($userInfo['city'])
                    ->set('gender')->stringIf($userInfo['sex'] ?? $userInfo['gender']);
            }

            // start register
            $addUser =  isset($data) ? User::common()->add($data) : User::common()->add( DBValues::init('wechatid')->string($weChatID) ) ;

            if (!$addUser->isSucceed()){ return $addUser;}

            $userid = $addUser->getContent();

        }else{

            // start login
            $getUserid = User::common()->getUserid('wechatid',$weChatID);

            if (!$getUserid->isSucceed()){ return $getUserid;}

            $userid = $getUserid->getContent();
        }

        $authorize = AccessToken::common()->addToken( $userid, $scope );

        _ASRecord()->add([
            'itemid'=>$userid,
            'type'=>'user',
            'event'=>'USER_LOGIN_WECHAT',
            'sign'=>'WEIXIN::oauthLogin',
            'content'=>['userid'=>$userid,'expire'=>getConfig('LOGINTOKEN_DURATION')+time()]
        ]);

        return $this->take(['userid'=>$userid,'token'=>$authorize->getContent()['token'],'scope'=>$scope,'expire'=>$authorize->getContent()['expire']])->success(i18n('USR_LOG_SUC'),'WEIXIN::oauthLogin');

    }


    /**
     * 绑定微信
     * bind wechatid to user account
     * @param $params
     * @return ASResult
     */
    public function bind( $params ): ASResult
    {

        $userInfo = isset($params['unionid'])||isset($params['openid']) ? $params : $this->getInfo($params,'bindWechat');
        $weChatID = $userInfo['unionid'] ?? $userInfo['openid'];

        if ( !User::common()->has(DBConditions::init()->where('wechatid')->equal($weChatID) ) ) { # 未注册

            $bind = User::common()->update( DBValues::init('wechatid')->string($weChatID), $params['userid']);
            return $bind->isSucceed() ? $this->success(i18n('SYS_SUC')) :$this->error(500,'Bind Failed') ;

        }else{

            return $this->take($weChatID)->error(601,'ERROR_WX_BOUND','WEIXIN::bind');
        }
    }


    /**
     * 公众号jsapi下单
     * jsapiPay
     * @param $openid
     * @param $order
     * @return ASResult
     */
    public function jsapiPay( string $openid, CommerceOrder $order ): ASResult
    {

        $wechat = new Pay($this->getConfig());

        $options = [
            'body'             => $order->title,
            'out_trade_no'     => $order->uid,
            'total_fee'        => Filter::priceToInt($order->amount),
            'openid'           => $openid,

            // 'attach'           => '',
            // 'detail'           => '',
            // 'time_start'       => (string)date("YmdHis"),
            // 'time_expire'      => (string)date("YmdHis", time() + PAYMENT_VALIDTIME),
            // 'goods_tag'        => isset($order['vipdiscount'])?'VIP折扣优惠':'',
            // 这个版本微信插件不支持

            'trade_type'       => 'JSAPI',
            'notify_url'       => getConfig('API_PATH')."commerce/wechatPayCallback",
            'spbill_create_ip' => $_SERVER['REMOTE_ADDR'],
        ];

        try {
            $result = $wechat->createOrder($options);
            $params  = $wechat->createParamsForJsApi($result['prepay_id']);
            return $this->take($params)->success();
        } catch (InvalidResponseException $e) {
            $exception = $e;
        } catch (LocalCacheException $e) {
            $exception = $e;
        }

        return $this->take($exception)->error(650,'Create Order Failed','Wechat->jsapiPay');
    }



    /**
     * 支付完成回调
     * paymentCallback
     */
    public function paymentCallback(){

        $wechat   = new Pay($this->getConfig());
        try {
            $result = $wechat->getNotify();
//            $orderid = $result['out_trade_no'];

            //第二：执行业务逻辑

            $data = static::getResultData($result);

            _ASRecord()->add([
                'category'=>'system',
                'content'=>$data,
                'event'=>'WECHATPAY_CALLBACK',
                'sign'=>'WEIXIN::paymentCallback'
            ]);

            CommercePayment::common()->callback( $data );

            // 返回接收成功的回复
            ob_clean();
            echo $wechat->getNotifySuccessReply();
            exit();

        } catch (InvalidResponseException $e) {

            _ASRecord()->add([
                'status'=>-10,
                'content'=>$e,
                'event'=>'WECHAT_PAYMENT_CALL',
                'sign'=>'Wechat->paymentCallback'
            ]);
            exit();
        }

    }


    /**
     * 发起微信退款
     * refund
     * @param  string    $orderid
     * @param  int|NULL  $refundAmount      单位统一为分 即 amount 100 = 1元
     * @return ASResult
     */
    public function refund( string $orderid, int $refundAmount = null ): ASResult
    {

        // 获得订单详情
        $order = CommerceOrder::instance($orderid);

        $wechat  = new Pay($this->getConfig());

        $options = [
            'body'             => $order['title'],
            'out_trade_no'     => $orderid,
            'total_fee'        => (int)$order['amount'],
            'refund_fee'       => $refundAmount ?? (int)$order['amount'],
            'notify_url'       => getConfig('API_PATH')."commerce/wechatPayRefundCallback",
        ];

        try {
            $wechat->createRefund($options);

            $order->beginRefund($orderid);

            return $this->take($orderid)->success(i18n('ORD_REF_SUC'),'Wechat->refund');

        } catch (InvalidResponseException $e) {
            $exception = $e;
        } catch (LocalCacheException $e) {
            $exception = $e;
        }

        return $this->take($exception)->error(12300,i18n('ORD_REF_FAL'),'Wechat->refund');
    }


    /**
     * 退款回调
     * refundCallback
     * @return ASResult
     */
    public function refundCallback(): ASResult
    {

        $wechat   = new Pay($this->getConfig());
        try {
            $result = $wechat->getNotify();

            $orderid = $result['out_trade_no'];
            $data = static::getResultData($result);

            $order = CommerceOrder::instance($orderid);

            // 1.检查是否在退款中
            if ( $order->status !== 'refunding' ) {

                return $this->error(9032,'ORD_REF_DONE','Wechat->refundCallback');
            }

            $call = $order->refundCall($orderid);

            if( !$call->isSucceed() ){

                _ASRecord()->add(['content'=>$call,'status'=>12345,'event'=>'REFUND_CALLBACK']);

                return $this->error(12345,'业务逻辑错误','Wechat->refundCallback:call');
            }

            $order->update( DBValues::init('refundid')->stringIf($data['paymenttradeno'])->set('status')->string('refunded'), $orderid );

            // 2.3 进行回复
            ob_clean();
            echo $wechat->getNotifySuccessReply();
            exit();

        } catch (InvalidResponseException $e) {
            $ex = $e;
        }

        return $this->take($ex)->error(12000,'REFUND FAL');

    }


    /**
     * 转账到openid
     * transfers
     * @param  string  $openid
     * @param  float   $amount
     * @param  string  $description
     * @return mixed
     */
    public function transfers( string $openid , float $amount, $description = '付款到微信钱包' ){

        $wechat = new Pay($this->getConfig());

        $options = [
            'partner_trade_no' => time(),
            'openid'           => $openid,
            'check_name'       => 'NO_CHECK',
            'amount'           => Filter::priceToInt($amount),
            'desc'             => $description,
            // 'spbill_create_ip' => $_SERVER['REMOTE_ADDR'],
            'spbill_create_ip' => getConfig('SERVER_IP'),
        ];

        try {
            $transfer = $wechat->createTransfers($options);
        } catch (InvalidResponseException $e) {

            $GLOBALS['record']->add([
                'content'=>['result'=>$e,'info'=>$options],
                'status'=>433,'event'=>'WXPOCKET_TRANSFER','type'=>'wechatTransfer']);

            // 按照微信官方提示 在有错误码时进行二次重试 (同一订单号)
            try {
                $transfer = $wechat->createTransfers($options);
            } catch (InvalidResponseException $e) {
                $ex = $e;
            } catch (LocalCacheException $e){
                $ex = $e;
            }

        } catch (LocalCacheException $e) {
            $ex = $e;
        }
        // 进行付款

        if( isset($transfer) ){

            // 查询付款是否成功
            try {

                $wechat->queryTransfers($options['partner_trade_no']);

                return $this->take($options['partner_trade_no'])->success('Transfer Succeed.','Wechat->transfers');

            } catch (InvalidResponseException $e) {
                $ex = $e;
            } catch (LocalCacheException $e) {
                $ex = $e;
            }
        }

        return $this->take($ex)->error(9896,'Transfer Failed.','Wechat->transfers');

    }



    /**
     * 获取微信jssdk config
     * getJssdkConfig
     * @param $url
     * @return ASResult
     */
    public function getJssdkConfig($url): ASResult
    {

        $wechat = new Script($this->getConfig());

        try {
            $result = $wechat->getJsSign($url);

            return $this->take($result)->success();
        } catch (InvalidResponseException $e) {
            $ex = $e;
        } catch (LocalCacheException $e) {
            $ex = $e;
        }
        return $this->take($ex)->error(700,'Get config Failed.');
    }




    public static function getResultData( array $result ): array
    {

        $data['paymenttradeno'] =  $result['transaction_id']         ;  // 微信支付订单号
        $data['orderid']        =  $result['out_trade_no']           ;  // 订单号
        $data['paytime']        =  $result['time_end']               ;  // 交易完成时间
        $data['amount']         =  Filter::priceToFloat($result['total_fee']);  // 支付金额 (微信单位为分)
        $data['feetype']        =  $result['fee_type']               ;  // 货币类型
        $data['paymenttype']    =  $result['trade_type']             ;  // 交易类型
        $data['payment']        =  'wechatPay';

        return $data;
    }



}