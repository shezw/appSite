<?php

namespace APS;

use PluploadHandler;

require_once( LIB_DIR . "pluploadHandler/PluploadHandler.php");


/**
 * @requires php extension fileinfo
 * Class Uploader
 * @package APS
 */
class Uploader extends ASModel {

    private $keyId;
    private $secret;

    function __construct(string $accessKeyId = null, string $accessKeySecret = null, string $endpoint = null)
    {
        parent::__construct();

        $this->keyId = $accessKeyId ?? getConfig('KEYID','INNER_UPLOADER');
        $this->secret = $accessKeySecret ?? getConfig('KEYSECRET','INNER_UPLOADER');

    }

    public function getSign( string $type = null ): ASResult
    {

        $host = getConfig('API_PATH') . "common/upload";

        $callbackUrl = getConfig('API_PATH') . "common/uploadCallback";

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

        $dir = $type ? "$type/$month/" : "unknown/$month/";

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
        $signature = base64_encode(hash_hmac('sha1', $string_to_sign, $this->secret, true));

        $response = [

            'accessid' => $this->keyId,
            'host' => $host,
            'policy' => $base64_policy,
            'signature' => $signature,
            'expire' => $expire,
            'callback' => $base64_callback_body,
            //这个参数是设置用户上传指定的前缀
            'dir' => $dir,

        ];

        $this->record('Uploader_Sign','Uploader->policySign',$response);

        return $this->take(json_encode($response))->success(i18n('SIGN_SUCCESS'),'Uploader::getSign');

    }


    private function checkSignature( $policy, $sign = null ){

        $signature = base64_encode(hash_hmac('sha1', $policy, $this->secret, true));

        if ( $signature !== $sign ){
            die("Signature Failed.");
        }

    }


    public function receive( $params ){

        $this->checkSignature($params['policy'], $params['signature'] );

        $dir = STATIC_DIR.'uploads/';
        $filename = $params['key'] ?? $params['name'];

        $ph = new PluploadHandler(array(
            'target_dir' => $dir,
            'file_name' => $params['key'] ?? $params['name'],
            'allow_extensions' => getConfig( "MEDIA_FILE_TYPE").','.getConfig( "MEDIA_IMAGE_TYPE").','.getConfig( "MEDIA_AUDIO_TYPE").','.getConfig( "MEDIA_VIDEO_TYPE")
        ));

        if ($result = $ph->handleUpload()) {

            $data = ["Status" => "Ok",
                "content" => [
                    'name' => $result['name'],
                    'size' => $result['size'],
                    'type' => static::checkValidMIME( $result['path'] ),
                    'url' => getConfig('STATIC_PATH'). 'uploads/' . $filename,
                    'server'=>StorageLocation_LocalStatic
                ]
            ];

            $mediaDetails = [ // 媒体信息
                'url' => $data['content']['url'],
                'size' => $data['content']['size'],
                'type' => $data['content']['type'],
                'status' => 'enabled',
                'server'=>StorageLocation_AliOSS
            ];

            # 在媒体库进行添加
            $newMedia = Media::common()->addByArray($mediaDetails );

            # 返回媒体ID
            $data['content']['uid'] = $newMedia->getContentOr(null);

            $ph->sendCORSHeaders();
            $ph->sendNoCacheHeaders();

        } else {

            header("http/1.1 403 Forbidden");

            $data = [
                'OK' => 0,
                'error' => [
                    'code' => $ph->getErrorCode(),
                    'message' => $ph->getErrorMessage()
                ]
            ];
        }

        die(json_encode($data));
    }


    public function removeFile( string $url ): ASResult
    {

        $file = str_replace( getConfig('STATIC_PATH'), STATIC_DIR, $url );

        return $this->take( @unlink($file) )->feedback();
    }





    public static function checkValidMIME( string $filePath ): string
    {

        $fileInfo = finfo_open(FILEINFO_MIME_TYPE); // 返回 mime 类型
        $mime  = finfo_file($fileInfo, $filePath);
        finfo_close($fileInfo);

        $type = static::checkMediaType( $mime );

        if( !$mime || !$type ){
            header("http/1.1 403 Bad File Forbidden");
            die("{$mime} File Illegal.");
        }else{
            return $type;
        }

    }


    /**
     * 通过文件类型获取类别
     * checkMediaType
     * @param $fullType
     * @return string
     */
    public static function checkMediaType($fullType): string
    {

        $types = [
            'document' => 'application/json|json|application/mp4|application/ogg|application/pdf|pdf|application/PDX|PDX|application/rtf|rtf|application/rtx|rtx|application/sql|sql|application/xml|xml|application/zip|zip|application/zlib|zlib|application/zstd|zstd|application/x-compressed|tgz|application/x-gtar|gtar|application/x-javascript|js|application/x-pkcs12|p12|application/x-tar|tar|application/x-x509-ca-cert|cer|application/msword|doc|application/vnd.ms-excel|xls|application/vnd.ms-project|mpp|application/vnd.ms-powerpoint|ppt|application/x-iwork-pages-sffpages|pages|application/x-iwork-numbers-sffnumbers|numbers|application/x-iwork-keynote-sffkeynote|keynote|application/vnd.openxmlformats-officedocument.wordprocessingml.document|docx|application/vnd.openxmlformats-officedocument.spreadsheetml.sheet|xlsx|application/vnd.openxmlformats-officedocument.presentationml.presentation|pptx|text/css|css|text/csv|csv|text/html|html|text/javascript|js|text/markdown|markdown|text/plain|txt|text/rtf|rtf|text/rtx|rtx|text/sgml|sgml|text/xml|xml',
            'image' => 'image/bmp|bmp|image/gif|gif|image/jpeg|jpeg|image/png|png|image/x-png|png|image/x-icon|ico|image/svg|svg|image/tiff|tiff|image/jpg|jpg|image/svg+xml|svg|image/xml',
            'audio' => 'audio/3gpp|3gpp|audio/3gpp2|3gpp2|audio/aac|aac|audio/ac3|ac3|audio/AMR|AMR|audio/MPA|MPA|audio/mp4|audio/mpeg|audio/ogg|ogg|audio/x-wav|wav|audio/mid|mid|audio/mpeg|audio/x-aiff|aiff|audio/x-wav|wav|audio/x-mpegurl|m3u|audio/x-pn-realaudio|ram|mp3',
            'video' => 'video/H261|H261|video/H263|H263|video/H263-1998|H263|video/H263-2000|H263|video/H264|H264|video/H264-RCDO|H264|video/H264-SVC|H264|video/H265|H265|video/JPEG|video/jpeg2000|video/mp4|mp4|video/MPV|MPV|video/mpeg|mpeg|video/ogg|ogg|video/quicktime|mov|video/x-msvideo|avi|video/mpeg4-generic|mp4'
        ];

        foreach ($types as $k => $v) {

            if (strstr($v, $fullType)) {
                return $k;
            }
        }
        return "";
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

        $file = Uploader::downloadFile($url, 1, 5)['content'];

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
    public static function downloadFile(string $url, int $type = 0, int $timeout = 15): ASResult
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


}