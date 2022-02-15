<?php

namespace APS;

use OSS\Core\OssException;
use OSS\OssClient;

include LIB_DIR."aliyun-oss-php-sdk/autoload.php";


/**
 * 阿里云OSS云存储
 * AliyunOSS
 *
 * @package APS\extension
 */
class AliyunOSS extends ASModel{

    private $accessKeyId;
    private $accessKeySecret;
    private $endpoint;

    /**
     * OSS客户端
     * @var OssClient
     */
    private $ossClient;

    function __construct(string $accessKeyId = null, string $accessKeySecret = null, string $endpoint = null)
    {
        parent::__construct();

        $this->accessKeyId = $accessKeyId ?? getConfig('OSS_KEYID','ALIYUN');
        $this->accessKeySecret = $accessKeySecret ?? getConfig('OSS_KEYSECRET','ALIYUN');
        $this->endpoint = $endpoint ?? getConfig('OSS_ENDPOINT','ALIYUN');
    }

    /**
     * Get an OSSClient instance according to config.
     * @return ASResult
     */
    public function initOssClient(): ASResult
    {
        try {
            $this->ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint, false);
        } catch (OssException $e) {

            return $this->error(2201, "creating OssClient instance: FAILED");
        }
        return $this->success();
    }



    /**
     * 生成签名 Generate Policy Sign
     * @param  string|null  $type  文件类型 File Type
     * @return ASResult
     */
    public function policySign(string $type = null): ASResult
    {

        $host = getConfig('CUSTOM_OSS_DOMAIN','ALIYUN')
            ? getConfig('CUSTOM_OSS_DOMAIN','ALIYUN')
            : 'https://' . getConfig('OSS_BUCKET','ALIYUN') . '.' . getConfig('OSS_ENDPOINT','ALIYUN');

        $callbackUrl = getConfig('API_PATH') . "aliyun/OSSCallback";

        $callback_param = [
            'callbackUrl' => $callbackUrl,
            'callbackBody' => 'filename=${object}&fileType=' . ($type ?? 'image') . '&size=${size}&mimeType=${mimeType}&width=${imageInfo.width}&height=${imageInfo.height}',
            'callbackBodyType' => "application/x-www-form-urlencoded",
        ];
        $callback_string = json_encode($callback_param);

        $base64_callback_body = base64_encode($callback_string);
        $now = time();
        $duration = getConfig('MEDIA_POLICY_DURATION');  //设置该policy有效时间
        $expire = $now + $duration;
        $expiration = Time::ISO8601($expire);

        $time = new Time();
        $month = $time->formatOutput(TimeFormat_NumberMonth);

        $dir = getConfig('APP_NAME')??'AppSite';
        $dir .= $dir ? '/' : '';
        $dir .= $type ? "res/$type/$month/" : "res/unknow/$month/";

        //最大文件大小.用户可以自己设置
        $condition = ['content-length-range', 0, getConfig('MEDIA_MAX_SIZE')];
        $conditions[] = $condition;

        //表示用户上传的数据,必须是以$dir开始, 不然上传会失败,这一步不是必须项,只是为了安全起见,防止用户通过policy上传到别人的目录
        // $start = array(0=>'starts-with', 1=>'$key',2=> $dir);
        // $conditions[] = $start;

        $arr = [
            'expiration' => $expiration,
            'conditions' => $conditions,
        ];

        $policy = json_encode($arr);
        $base64_policy = base64_encode($policy);
        $string_to_sign = $base64_policy;
        $signature = base64_encode(hash_hmac('sha1', $string_to_sign, $this->accessKeySecret, true));

        $response = [

            'accessid' => $this->accessKeyId,
            'host' => $host,
            'policy' => $base64_policy,
            'signature' => $signature,
            'expire' => $expire,
            'callback' => $base64_callback_body,
            //这个参数是设置用户上传指定的前缀
            'dir' => $dir,

        ];

        $this->record('AliyunOSS_Sign','AliyunOSS->policySign',$response);

        return $this->take(json_encode($response))->success(i18n('SIGN_SUCCESS'),'AliyunOSS::policySign');

    }


    /**
     * 回调接收 OSS Callback receive
     * @return void 对阿里云返回json格式 Output JSON for aliyun OSS
     */
    public static function callback(){

        // 1.获取OSS的签名header和公钥url header
        $authorizationBase64 = "";
        $pubKeyUrlBase64 = "";
        /*
         * 注意：如果要使用HTTP_AUTHORIZATION头，你需要先在apache或者nginx中设置rewrite，以apache为例，修改
         * 配置文件/etc/httpd/conf/httpd.conf(以你的apache安装路径为准)，在DirectoryIndex index.php这行下面增加以下两行
            RewriteEngine On
            RewriteRule .* - [env=HTTP_AUTHORIZATION:%{HTTP:Authorization},last]
         * */
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authorizationBase64 = $_SERVER['HTTP_AUTHORIZATION'];
        }
        if (isset($_SERVER['HTTP_X_OSS_PUB_KEY_URL'])) {
            $pubKeyUrlBase64 = $_SERVER['HTTP_X_OSS_PUB_KEY_URL'];
        }

        if ($authorizationBase64 == '' || $pubKeyUrlBase64 == '') {

            _ASRecord()->add([
                'content' => 'OSS error authorization!',
                'status' => 702,
                'event' => 'AliyunOSS_callback',
                'category' => 'error'
            ]);
            exit();
        }

        // 2.获取OSS的签名
        $authorization = base64_decode($authorizationBase64);

        // 3.获取公钥
        $pubKeyUrl = base64_decode($pubKeyUrlBase64);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $pubKeyUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $pubKey = curl_exec($ch);
        if ($pubKey == "") {
            //header("http/1.1 403 Forbidden");
            exit();
        }

        // 4.获取回调body
        $body = file_get_contents('php://input');
        parse_str($body, $file);    // 将字符串转换为数组

        // 5.拼接待签名字符串
        $authStr = '';
        $path = $_SERVER['REQUEST_URI'];
        $pos = strpos($path, '?');
        if ($pos === false) {

            $authStr = urldecode($path) . "\n" . $body;

        } else {

            $authStr = urldecode(substr($path, 0, $pos)) . substr($path, $pos, strlen($path) - $pos) . "\n" . $body;
        }

        // 6.验证签名
        $ok = openssl_verify($authStr, $authorization, $pubKey, OPENSSL_ALGO_MD5);

        // $ok =1;

        if ($ok == 1) { // 验证成功

            $host = getConfig('CUSTOM_OSS_DOMAIN','ALIYUN') ??
                    '//' . getConfig('OSS_BUCKET','ALIYUN') . '.' . getConfig('OSS_ENDPOINT','ALIYUN');

            // $GLOBALS['record']->add(['content'=>'aliyun oss Authorization OK!','status'=>0,'event'=>'alioss_callback']);
            // $fileType = ALIYUNOSS::checkMediaType($file['mimeType']);

            $data = [  // 初始化返回数据
                "Status" => "Ok",
                "content" => [
                    'name' => $file['filename'],
                    'size' => $file['size'],
                    'type' => Uploader::checkMediaType($file['mimeType']) ?? $file['fileType'],
                    'url' => $host . '/' . $file['filename'],
                    // 'url'  => getConfig('HOST_HTTPS'].getConfig('ALIYUNRESHOST'].$file['filename'],
                ]
            ];

            $mediadata = [ // 媒体信息
                'url' => $data['content']['url'],
                'size' => $data['content']['size'],
                'type' => $data['content']['type'],
                'status' => 'enabled',
                'server'=>StorageLocation_AliOSS
            ];

            // 元信息
            if (isset($file['width']) && isset($file['height'])) {
                if ($file['width'] !== '' && $file['height'] !== '') {
                    $mediadata['meta'] = ['width' => $file['width'], 'height' => $file['height']];
                }
            }

            # 在媒体库进行添加
            $newMedia = Media::common()->addByArray($mediadata );

            # 返回媒体ID
            $data['content']['uid'] = $newMedia->getContentOr(null);

            _ASRecord()->add(['content' => $data, 'event' => 'AliyunOSS_CALLBACK', 'sign' => 'AliyunOSS->callback']);

            header("Content-Type: application/json");
            echo json_encode($data);
            exit();

        } else {  // 验证失败

            _ASRecord()->add(['content' => $body, 'status' => 700, 'event' => 'AliyunOSS_CALLBACK', 'sign' => 'AliyunOSS->callback']);
            header("http/1.1 403 Forbidden");
            exit();
        }

    }



    /**
     * 上传指定的网络文件
     * Upload file to OSS by url
     * @param  string  $url
     * @param  string  $path
     * @return ASResult
     */
    public function uploadUrlFile(string $url, string $path = 'res'): ASResult
    {
        $file = Uploader::downloadFile($url, 1, 5);

        if (!$file->isSucceed()) {

            // 如果失败再一次尝试
            $file = Uploader::downloadFile($url, 1, 5);
        }

        // 再次失败则加入系统错误日志
        if (!$file->isSucceed()) {
            _ASRecord()->add([
                'category' => 'system',
                'status' => 500,
                'event' => 'OSS_FILE_GETFAL',
                'content' => ['url' => $url, 'file' => $file],
                'sign' => 'AliyunOSS->uploadUrlFile',
            ]);
            return $this->error(500,'OSS_FILE_GETFAL');
        }

        $base64 = $file->getContent()['data'];
        $type = $file->getContent()['type'];
        // $c         = $file->$this->getContent()['contentType'];

        $filetype = Uploader::checkMediaType($file->getContent()['contentType']);

        $filename = Encrypt::shortId(10);

        $time = new Time();
        $month = $time->formatOutput(TimeFormat_NumberMonth);

        $object = "$path/$filetype/$month/$filename.$type";

        if (File::addFile(getConfig('MEDIA_TEMP_DIR') . "$filename.$type")) {

            $file = getConfig('MEDIA_TEMP_DIR') . "$filename.$type";
            file_put_contents($file, base64_decode($base64));

        } else {

            return $this->take($url)->error(2189,'Temp file failed');
        }

        $ossClient = $this->initOssClient();
        $bucket = getConfig('OSS_BUCKET','ALIYUN');

        if( !$ossClient->isSucceed() ){ return $ossClient; }

        try {
            $this->ossClient->uploadFile($bucket, $object, $file);
        } catch (OssException $e) {

            $this->result->setStatus(796);
            $this->result->setMessage($e->getMessage()??'Failed');
        }

        File::removeFile($file);

        return $this->take('//' . getConfig('OSS_BUCKET','ALIYUN') . '.' . getConfig('OSS_ENDPOINT','ALIYUN') . '/' . $object)->success();
    }

    /**
     * 从OSS删除文件
     * Remove file from OSS
     * @param  string  $fileURL  完整链接 url
     * @return ASResult
     */
    public function removeFile(string $fileURL): ASResult
    {

        $ossClient = $this->initOssClient();
        $bucket = getConfig('OSS_BUCKET','ALIYUN');

        if( !$ossClient->isSucceed() ){ return $ossClient; }

        $file = static::getFileName($fileURL);
        try {
            $this->ossClient->deleteObject($bucket, $file);
        } catch (OssException $e) {

            $this->result->setStatus(799);
            $this->result->setMessage($e->getMessage()??'Failed');
        }

        $this->record();

        return $this->take($fileURL)->feedback();
    }


    /**
     * 从链接获取文件路径
     * Get pathurl from URL
     * @param  string       $fullUrl  链接地址
     * @param  string|null  $bucket   手动bucket
     * @return   string                                   路径 pathurl
     */
    public static function getFileName(string $fullUrl, string $bucket = null): string
    {
        if( getConfig('CUSTOM_OSS_DOMAIN','ALIYUN') ){
            $fullUrl = str_replace(getConfig('CUSTOM_OSS_DOMAIN','ALIYUN'),'',$fullUrl );
        }

        $url = str_replace("http://", "", $fullUrl);
        $url = str_replace("https://", "", $url);
        $url = str_replace(getConfig('OSS_BUCKET','ALIYUN') . ".", "", $url);
        $url = str_replace($bucket ?? getConfig('OSS_ENDPOINT','ALIYUN') . "/", "", $url);

        $url = '&/'.$url;
        $url = str_replace('&//','',$url);
        $url = str_replace('&/','',$url);

        return $url;
    }


}
