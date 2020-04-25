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
     * @return \APS\ASResult
     */
    public function validReceiptData( string $receipt, bool $isSandbox = false) {

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
                $message = i18n('SYS_FOMAT_ERROR');
                $content = $response;

            }else{

                //购买信息
                $in_app = $data['receipt']['in_app'][0];

            }
            //判断购买时候成功
            if (!isset($data['status']) || $data['status'] != 0) {

                $status = $data['status'] ?? 553;
                $message = ['IAP_VAD_FAL'];

                _ASRecord()->add(['content'=>$data,'itemid'=>$in_app['product_id'],'status'=>$data['status']??20000,'event'=>'IAP_VERIFY','sign'=>'validReceiptData']);
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

}