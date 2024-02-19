<?php

namespace APS;

/**
 * 苹果内购验证
 * IAP Validation
 * @package APS\extension
 */
class IAP extends ASObject {

    /**
     * 去苹果服务器二次验证代码
     * validReceiptData
     * @param  string  $receipt
     * @param  bool    $isSandbox
     * @return ASResult
     */
    public function validReceiptData( string $receipt, bool $isSandbox = false): ASResult
    {

        $endpoint = $isSandbox ?
            'https://sandbox.itunes.apple.com/verifyReceipt' : //沙箱地址
            'https://buy.itunes.apple.com/verifyReceipt'; //真实运营地址

        $postData = '{"receipt-data" : "'.$receipt.'"}';

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);  //这两行一定要加，不加会报SSL 错误
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $response = curl_exec($ch);
        $errno    = curl_errno($ch);
        //$errmsg   = curl_error($ch);
        curl_close($ch);

        $status  = 0;
        $message = i18n("IAP_VAD_SUC");
        $content = null;
        $in_app  = null;

        if ($errno != 0) {//curl请求有错误

            $status = 551;
            $message = i18n('SYS_TIMEOUT');
            $content = $receipt;

        }else{

            $data = json_decode($response, true);

            if (!is_array($data)) {

                $status  = 552;
                $message = i18n('SYS_FORMAT_ERROR');
                $content = $response;

            }else{

                //购买信息
                $in_app = $data['receipt']['in_app'][0];

            }
            //判断购买时候成功
            if (!isset($data['status']) || $data['status'] != 0) {

                $status = $data['status'] ?? 553;
                $message = ['IAP_VAD_FAL'];

                _ASRecord()->save(['content'=>$data,'itemid'=>$in_app['product_id'],'status'=>$data['status']??20000,'event'=>'IAP_VERIFY','sign'=>'validReceiptData']);
            }

            if( $status == 21007 && !$isSandbox ){
                return $this->validReceiptData($receipt,true);
            }

        }

        if( isset($in_app) ){ // 成功支付返回支付详情

            $content = [
                'itemid'    => $in_app['product_id'],
                'paymenttradeno' => $in_app['transaction_id'],
                'paytime'   => floor((int)$in_app['purchase_date_ms']/1000),

                'bundleId' => $data['receipt']['bundle_id'],
                'appVersion' => $data['receipt']['application_version'],
                'adamId' => $data['receipt']['adam_id'],
                'appItemId' => $data['receipt']['app_item_id'],
                'receiptType' => $data['receipt']['receipt_type'],
            ];
            $timer = new Time((int)$content['paytime']);
            $content['paytime_'] = $timer->customOutput('Y-m-d H:i s');;
        }

        return $this->take($content)->feedback($status,$message,'IAP::validReceiptData');
    }

    /**
     * 服务器二次验证代码
     * 21000 App Store不能读取你提供的JSON对象
     * 21002 receipt-data域的数据有问题
     * 21003 receipt无法通过验证
     * 21004 提供的shared secret不匹配你账号中的shared secret
     * 21005 receipt服务器当前不可用
     * 21006 receipt合法，但是订阅已过期。服务器接收到这个状态码时，receipt数据仍然会解码并一起发送
     * 21007 receipt是Sandbox receipt，但却发送至生产系统的验证服务
     * 21008 receipt是生产receipt，但却发送至Sandbox环境的验证服务
     */

    /*返回数据参照样例
    [
        'status' => 0,
        'environment' => 'Sandbox',
        'receipt' =>
        [
            'receipt_type' => 'ProductionSandbox',
            'adam_id' => 0,
            'app_item_id' => 0,
            'bundle_id' => 'com.abcde.www',
            'application_version' => '0.0.9',
            'download_id' => 0,
            'version_external_identifier' => 0,
            'receipt_creation_date' => '2016-07-13 18:22:19 Etc/GMT',
            'receipt_creation_date_ms' => '1468434139000',
            'receipt_creation_date_pst' => '2016-07-13 11:22:19 America/Los_Angeles',
            'request_date' => '2016-07-13 18:22:22 Etc/GMT',
            'request_date_ms' => '1468434142143',
            'request_date_pst' => '2016-07-13 11:22:22 America/Los_Angeles',
            'original_purchase_date' => '2013-08-01 07:00:00 Etc/GMT',
            'original_purchase_date_ms' => '1375340400000',
            'original_purchase_date_pst' => '2013-08-01 00:00:00 America/Los_Angeles',
            'original_application_version' => '1.0',
            'in_app' =>
            [
                [
                'quantity' => '1',
                'product_id' => 'price_1',
                'transaction_id' => '1000000223463280',
                'original_transaction_id' => '1000000223463280',
                'purchase_date' => '2016-07-13 18:22:19 Etc/GMT',
                'purchase_date_ms' => '1468434139000',
                'purchase_date_pst' => '2016-07-13 11:22:19 America/Los_Angeles',
                'original_purchase_date' => '2016-07-13 18:22:19 Etc/GMT',
                'original_purchase_date_ms' => '1468434139000',
                'original_purchase_date_pst' => '2016-07-13 11:22:19 America/Los_Angeles',
                'is_trial_period' => 'false',
                ],
            ],
        ],
    ]
    */

    /**
    auto_renew_adam_id					string
    auto_renew_product_id				string
    auto_renew_status					string
    auto_renew_status_change_date		string
    auto_renew_status_change_date_ms	string
    auto_renew_status_change_date_pst	string
    environment							string		Sandbox, PROD
    expiration_intent					int
    bid 								string 		A string that contains the app bundle ID.
    bvrs								string 		A string that contains the app bundle version.

    password							string 		The same value as the shared secret you submit in the password field of the requestBody when validating receipts.

    unified_receipt		An object that contains information about the most-recent, in-app purchase transactions for the app.

    notification_type		string

    CANCEL
    Indicates that either Apple customer support canceled the subscription or the user upgraded their subscription. The cancellation_date key contains the date and time of the change.

    DID_CHANGE_RENEWAL_PREF
    Indicates that the customer made a change in their subscription plan that takes effect at the next renewal. The currently active plan isn’t affected.

    DID_CHANGE_RENEWAL_STATUS
    Indicates a change in the subscription renewal status. In the JSON response, check auto_renew_status_change_date_ms to know the date and time of the last status update. Check auto_renew_status to know the current renewal status.

    DID_FAIL_TO_RENEW
    Indicates a subscription that failed to renew due to a billing issue. Check is_in_billing_retry_period to know the current retry status of the subscription. Check grace_period_expires_date to know the new service expiration date if the subscription is in a billing grace period.

    DID_RECOVER
    Indicates a successful automatic renewal of an expired subscription that failed to renew in the past. Check expires_date to determine the next renewal date and time.

    DID_RENEW
    Indicates that a customer’s subscription has successfully auto-renewed for a new transaction period.

    INITIAL_BUY
    Occurs at the user’s initial purchase of the subscription. Store latest_receipt on your server as a token to verify the user’s subscription status at any time by validating it with the App Store.

    INTERACTIVE_RENEWAL
    Indicates the customer renewed a subscription interactively, either by using your app’s interface, or on the App Store in the account’s Subscriptions settings. Make service available immediately.

    PRICE_INCREASE_CONSENT
    Indicates that App Store has started asking the customer to consent to your app’s subscription price increase. In the unified_receipt.Pending_renewal_infoobject, the price_consent_status value is 0, indicating that App Store is asking for the customer’s consent, and hasn’t received it. The subscription won’t auto-renew unless the user agrees to the new price. When the customer agrees to the price increase, the system sets price_consent_status to 1. Check the receipt using verifyReceipt to view the updated price-consent status.

    REFUND
    Indicates that App Store successfully refunded a transaction. The cancellation_date_ms contains the timestamp of the refunded transaction. The original_transaction_id and product_id identify the original transaction and product. The cancellation_reason contains the reason.

    REVOKE
    Indicates that an in-app purchase the user was entitled to through Family Sharing is no longer available through sharing. StoreKit sends this notification when a purchaser disabled Family Sharing for a product, the purchaser (or family member) left the family group, or the purchaser asked for and received a refund. Your app will also receive a paymentQueue(_:didRevokeEntitlementsForProductIdentifiers:)call. For more information about Family Sharing, see Supporting Family Sharing in Your App.




     */

    function notify(){

        $requestJSON = json_decode(file_get_contents("php://input"),true);

    }
}