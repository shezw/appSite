<?php

namespace APS;

use OSS\Core\OssException;
use OSS\OssClient;

include SERVER_DIR."library/aliyun-oss-php-sdk/autoload.php";


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
            $this->ossClient = new \OSS\OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint, false);
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
        $month = $time->formatOutput(TimeFormatEnum::NUMBER_MONTH);

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
                    'type' => static::checkMediaType($file['mimeType']) ?? $file['fileType'],
                    'url' => $host . '/' . $file['filename'],
                    // 'url'  => getConfig('HOST_HTTPS'].getConfig('ALIYUNRESHOST'].$file['filename'],
                ]
            ];

            $mediadata = [ // 媒体信息
                'url' => $data['content']['url'],
                'size' => $data['content']['size'],
                'type' => $data['content']['type'],
                'status' => 'enabled',
            ];

            // 元信息
            if (isset($file['width']) && isset($file['height'])) {
                if ($file['width'] !== '' && $file['height'] !== '') {
                    $mediadata['meta'] = ['width' => $file['width'], 'height' => $file['height']];
                }
            }

            # 在媒体库进行添加
            $newMedia = Media::common()->add($mediadata);
            if ($newMedia->isSucceed()) {
                $mediaId = $newMedia->getContent();
            }

            # 返回媒体ID
            $data['content']['mediaid'] = $mediaId;

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
     * 通过文件类型获取类别
     * checkMediaType
     * @param $fullType
     * @return int|string
     */
    public static function checkMediaType($fullType)
    {

        $types = [
            'document' => 'application/json|json|application/mp4|application/ogg|application/pdf|pdf|application/PDX|PDX|application/rtf|rtf|application/rtx|rtx|application/sql|sql|application/xml|xml|application/zip|zip|application/zlib|zlib|application/zstd|zstd|application/x-compressed|tgz|application/x-gtar|gtar|application/x-javascript|js|application/x-pkcs12|p12|application/x-tar|tar|application/x-x509-ca-cert|cer|application/msword|doc|application/vnd.ms-excel|xls|application/vnd.ms-project|mpp|application/vnd.ms-powerpoint|ppt|application/x-iwork-pages-sffpages|pages|application/x-iwork-numbers-sffnumbers|numbers|application/x-iwork-keynote-sffkeynote|keynote|application/vnd.openxmlformats-officedocument.wordprocessingml.document|docx|application/vnd.openxmlformats-officedocument.spreadsheetml.sheet|xlsx|application/vnd.openxmlformats-officedocument.presentationml.presentation|pptx|text/css|css|text/csv|csv|text/html|html|text/javascript|js|text/markdown|markdown|text/plain|txt|text/rtf|rtf|text/rtx|rtx|text/sgml|sgml|text/xml|xml',
            'image' => 'image/bmp|bmp|image/gif|gif|image/jpeg|jpeg|image/png|png|image/x-png|png|image/x-icon|ico|image/svg|svg|image/tiff|tiff|image/jpg|jpg|image/svg+xml|svg|image/xml',
            'audio' => 'audio/3gpp|3gpp|audio/3gpp2|3gpp2|audio/aac|aac|audio/ac3|ac3|audio/AMR|AMR|audio/MPA|MPA|audio/mp4|audio/mpeg|audio/ogg|ogg|audio/x-wav|wav|audio/mid|mid|audio/mpeg|audio/x-aiff|aiff|audio/x-wav|wav|audio/x-mpegurl|m3u|audio/x-pn-realaudio|ram|mp3',
            'video' => 'video/H261|H261|video/H263|H263|video/H263-1998|H263|video/H263-2000|H263|video/H264|H264|video/H264-RCDO|H264|video/H264-SVC|H264|video/H265|H265|video/JPEG|video/jpeg2000|video/mp4|mp4|video/MPV|MPV|video/mpeg|mpeg|video/ogg|ogg|video/quicktime|mov|video/x-msvideo|avi|video/mpeg4-generic|mp4'
        ];

        foreach ($types as $k => $v) {

            if (strstr($v, $fullType)) {
                $mediaType = $k;
            }
        }

        return $mediaType ?? null;
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

        $file = static::curlFile($url, 1, 5);

        if (!$file->isSucceed()) {

            // 如果失败再一次尝试
            $file = static::curlFile($url, 1, 5);

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

        $filetype = static::checkMediaType($file->getContent()['contentType']);

        $filename = Encrypt::shortId(10);

        $time = new Time();
        $month = $time->formatOutput(TimeFormatEnum::NUMBER_MONTH);

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
    public static function getFileName(string $fullUrl, string $bucket = null)
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

    /**
     * 转换URL为Base64编码
     * Convert URL to Base64
     * @Author   Sprite                   hello@shezw.com http://donsee.cn
     * @DateTime 2019-06-14T12:31:04+0800
     * @param  string   $url
     * @param  boolean  $decode
     * @return false|string
     */
    public static function getBase64(string $url, $decode = false){

        $file = AliyunOSS::curlFile($url, 1, 5)['content'];

        $base64 = $file['data'];

        return $decode ? base64_decode($base64) : $base64;

    }

    /**
     * CURL下载文件
     * Download file with CURL
     * @param  string       $url      链接地址
     * @param  int $type     数据类型  0,1,2,3
     * @param  int $timeout  超时
     * @return ASResult
     */
    public static function curlFile(string $url, int $type = 0, int $timeout = 15): ASResult
    {

        $msg = ['code' => 2100, 'status' => 'error', 'msg' => '未知错误！'];

        $types = [

            'application/octet-stream' => 'stream',

            // application
            'application/json' => 'json',
            'application/mp4' => 'mp4',
            'application/ogg' => 'ogg',
            'application/pdf' => 'pdf',
            'application/PDX' => 'PDX',
            'application/rtf' => 'rtf',
            'application/rtx' => 'rtx',
            'application/sql' => 'sql',
            'application/xml' => 'xml',
            'application/zip' => 'zip',
            'application/zlib' => 'zlib',
            'application/zstd' => 'zstd',
            'application/x-compressed' => 'tgz',
            'application/x-gtar' => 'gtar',
            'application/x-javascript' => 'js',
            'application/x-pkcs12' => 'p12',
            'application/x-tar' => 'tar',
            'application/x-x509-ca-cert' => 'cer',
            'application/msword' => 'doc',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.ms-project' => 'mpp',
            'application/vnd.ms-powerpoint' => 'ppt',
            'application/x-iwork-pages-sffpages' => 'pages',
            'application/x-iwork-numbers-sffnumbers' => 'numbers',
            'application/x-iwork-keynote-sffkeynote' => 'keynote',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',

            // text
            'text/css' => 'css',
            'text/csv' => 'csv',
            'text/html' => 'html',
            'text/javascript' => 'js',
            'text/markdown' => 'markdown',
            'text/plain' => 'txt',
            'text/rtf' => 'rtf',
            'text/rtx' => 'rtx',
            'text/sgml' => 'sgml',
            'text/xml' => 'xml',

            // image
            'image/bmp' => 'bmp',
            'image/gif' => 'gif',
            'image/jpeg' => 'jpeg',
            'image/png' => 'png',
            'image/x-png' => 'png',
            'image/x-icon' => 'ico',
            'image/svg' => 'svg',
            'image/tiff' => 'tiff',
            'image/jpg' => 'jpg',
            'image/svg+xml' => 'svg',
            'image/xml' => 'svg',

            // audio
            'audio/3gpp' => '3gpp',
            'audio/3gpp2' => '3gpp2',
            'audio/aac' => 'aac',
            'audio/ac3' => 'ac3',
            'audio/AMR' => 'AMR',
            'audio/MPA' => 'MPA',
            'audio/mp4' => 'mp4',
            'audio/mpeg' => 'mpeg',
            'audio/ogg' => 'ogg',
            'audio/x-wav' => 'wav',
            'audio/mid' => 'mid',
            'audio/mp3' => 'mp3',
            'audio/x-aiff' => 'aiff',
            'audio/x-mpegurl' => 'm3u',
            'audio/x-pn-realaudio' => 'ram',

            // video
            'video/H261' => 'H261',
            'video/H263' => 'H263',
            'video/H263-1998' => 'H263',
            'video/H263-2000' => 'H263',
            'video/H264' => 'H264',
            'video/H264-RCDO' => 'H264',
            'video/H264-SVC' => 'H264',
            'video/H265' => 'H265',
            'video/JPEG' => 'jpeg',
            'video/jpeg2000' => 'jpeg',
            'video/mp4' => 'mp4',
            'video/MPV' => 'MPV',
            'video/mpeg' => 'mpeg',
            'video/ogg' => 'ogg',
            'video/quicktime' => 'mov',
            'video/x-msvideo' => 'avi',
            'video/mpeg4-generic' => 'mp4',

        ];
        if (!stristr($url, 'http')) {
            $msg['code'] = 2101;
            $msg['msg'] = 'url地址不正确!';
            return ASResult::shared(2101,'Not valid url');
        }
        $dir = pathinfo($url);
        $host = $dir['dirname'];
        $refer = $host . '/';
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_REFERER, $refer); //伪造来源地址
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//返回变量内容还是直接输出字符串,0输出,1返回内容
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);//在启用CURLOPT_RETURNTRANSFER的时候，返回原生的（Raw）输出
        curl_setopt($ch, CURLOPT_HEADER, 0); //是否输出HEADER头信息 0否1是
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout); //超时时间

        $data = curl_exec($ch);
        $info = curl_getinfo($ch);

        curl_close($ch);
        $httpCode = intval($info['http_code']);
        $httpContentType = $info['content_type'];
        $httpSizeDownload = intval($info['size_download']);

        if ($httpCode != '200') {
            $msg['code'] = 2102;
            $msg['msg'] = 'url返回内容不正确！';
        }
        if ($type > 0 && !isset($types[$httpContentType])) {
            $msg['code'] = 2103;
            $msg['msg'] = 'url资源类型未知！';
        }
        if ($httpSizeDownload < 1) {
            $msg['code'] = 2104;
            $msg['msg'] = '内容大小不正确！';
        }
        if ($httpCode == '200') {
            $msg['code'] = 0;
            $msg['status'] = 'success';
            $msg['msg'] = '资源获取成功';
        }

        if ($info['content_type'] == 'application/octet-stream') {

            $fulltypes = [

                // application
                'json' => 'application/json',
                'pdf' => 'application/pdf',
                'PDX' => 'application/PDX',
                'sql' => 'application/sql',
                'zip' => 'application/zip',
                'zlib' => 'application/zlib',
                'zstd' => 'application/zstd',
                'tgz' => 'application/x-compressed',
                'gtar' => 'application/x-gtar',
                'p12' => 'application/x-pkcs12',
                'tar' => 'application/x-tar',
                'cer' => 'application/x-x509-ca-cert',
                'doc' => 'application/msword',
                'xls' => 'application/vnd.ms-excel',
                'mpp' => 'application/vnd.ms-project',
                'ppt' => 'application/vnd.ms-powerpoint',
                'pages' => 'application/x-iwork-pages-sffpages',
                'numbers' => 'application/x-iwork-numbers-sffnumbers',
                'keynote' => 'application/x-iwork-keynote-sffkeynote',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',

                // text
                'css' => 'text/css',
                'csv' => 'text/csv',
                'html' => 'text/html',
                'js' => 'text/javascript',
                'markdown' => 'text/markdown',
                'txt' => 'text/plain',
                'rtf' => 'text/rtf',
                'rtx' => 'text/rtx',
                'sgml' => 'text/sgml',
                'xml' => 'text/xml',

                // image
                'bmp' => 'image/bmp',
                'gif' => 'image/gif',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'ico' => 'image/x-icon',
                'svg' => 'image/svg',
                'tiff' => 'image/tiff',
                'jpg' => 'image/jpg',

                // audio
                '3gpp' => 'audio/3gpp',
                '3gpp2' => 'audio/3gpp2',
                'aac' => 'audio/aac',
                'ac3' => 'audio/ac3',
                'AMR' => 'audio/AMR',
                'MPA' => 'audio/MPA',
                'ogg' => 'audio/ogg',
                'mid' => 'audio/mid',
                'mp3' => 'audio/mpeg',
                'aiff' => 'audio/x-aiff',
                'wav' => 'audio/x-wav',
                'm3u' => 'audio/x-mpegurl',
                'ram' => 'audio/x-pn-realaudio',

                // video

                'H261' => 'video/H261',
                'H263' => 'video/H263',
                'H264' => 'video/H264',
                'H265' => 'video/H265',
                'mp4' => 'video/mp4',
                'MPV' => 'video/MPV',
                'mpeg' => 'video/mpeg',
                'mov' => 'video/quicktime',
                'avi' => 'video/x-msvideo',

            ];

            $filetype = pathinfo(parse_url($url)['path'])['extension'];
            $httpContentType = $fulltypes[$filetype];

        }

        if (strstr($httpContentType, 'image')) {

            if ($type == 0 || $httpContentType == 'text/html') $msg['data'] = $data;
            $base_64 = base64_encode($data);

            $type == 1 && $msg['data'] = $base_64;
            $type == 2 && $msg['data'] = "data:{$httpContentType};base64,{$base_64}";
            $type == 3 && $msg['data'] = "<img src='data:{$httpContentType};base64,{$base_64}' alt='img'/>";

        } else {
            $msg['data'] = $data;
        }

        unset($info, $data, $base_64);

        if (!isset($types[$httpContentType])) {

            return ASResult::shared(-1,'Unknow File Type',$httpContentType,'ALIYUNOSS::curlFile' );
        }

        return ASResult::shared($msg['code'], $msg['msg'], ['data' => $msg['data'], 'contentType' => $httpContentType, 'type' => $types[$httpContentType], 'msg' => $msg['msg']], 'ALIYUNOSS::curlFile');
    }


    /** Not Useful */
    /**
     * A tool function which creates a bucket and exists the process if there are exceptions
     */
//    public function createBucket()
//    {
//        $getOssClient = $this->initOssClient();
//        if( !$getOssClient->isSucceed() ){
//            return $getOssClient;
//        }
//        $bucket = getConfig('OSS_BUCKET');
//        $acl = OssClient::OSS_ACL_TYPE_PUBLIC_READ;
//
//        $ossClient = $getOssClient->getContent();
//        try {
//            $ossClient->createBucket($bucket, $acl);
//        } catch (OssException $e) {
//
//            $message = $e->getMessage();
//            if (\OSS\Core\OssUtil::startsWith($message, 'http status: 403')) {
//                $m = "Please Check your AccessKeyId and AccessKeySecret";
//            } elseif (strpos($message, "BucketAlreadyExists") !== false) {
//                $m = "Bucket already exists. Please check whether the bucket belongs to you, or it was visited with correct endpoint. ";
//            }
//
//            return $this->error(2104,$m);
//        }
//        return $this->success();
//    }


}
