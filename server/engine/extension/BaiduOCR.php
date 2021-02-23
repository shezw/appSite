<?php

namespace APS;

use AipOcr;

include SERVER_DIR."library/baidu_ocr/autoload.php";

/**
 * 百度文字识别
 * BaiduOCR
 * @package APS\extension
 */
class BaiduOCR extends ASObject{

    var $id;
    var $ak;
    var $sk;

    public function __construct(  string $id = null, string $ak = null, string $sk = null){
        parent::__construct();

        $this->id = $id ?? getConfig('ORC_BAIDU_ID','BAIDUYUN');
        $this->ak = $ak ?? getConfig('ORC_BAIDU_AK','BAIDUYUN');
        $this->sk = $sk ?? getConfig('ORC_BAIDU_SK','BAIDUYUN');
    }

    /**
     * 获取护照信息
     * Get passport by OCR
     * @param string $url
     * @return ASResult
     */
    public function passportOCR( string $url ): ASResult
    {

        $ocr = new AipOcr($this->id,$this->ak,$this->sk);
        $res = $ocr->passport(AliyunOSS::getBase64($url,true));

        if(!isset($res['words_result'])){
            return $this->take($res)->error(600,'No valid data return back','BaiduOCR->analysisPassport');
        }

        $passportInfo = [
            'passport'=>$url,
            'namecn'=>$res['words_result']['姓名']['words'],
            'nameen'=>$res['words_result']['姓名拼音']['words'],
            'gender'=>$res['words_result']['性别']['words'],
            'country'=>$res['words_result']['国家码']['words'],
            'passportno'=>$res['words_result']['护照号码']['words'],
            'sign_date'=>$res['words_result']['签发日期']['words'],
            'expire'=>$res['words_result']['有效期至']['words'],
            'sign_location'=>$res['words_result']['护照签发地点']['words'],
            'birth_location'=>$res['words_result']['出生地点']['words'],
            'birth_day'=>$res['words_result']['生日']['words'],
        ];

        return $this->take($passportInfo)->success('success','passportOCR');

    }

    /**
     * Get Company Data by OCR
     * 获取企业信息
     * @param string $url
     * @return ASResult
     */
    public function companyOCR( string $url ): ASResult
    {

        $ocr = new AipOcr($this->id,$this->ak,$this->sk);
        $res = $ocr->businessLicense(AliyunOSS::getBase64($url,true));

        if(!isset($res['words_result'])){
            return $this->take($res)->error(600,'No valid data return back','BaiduOCR->analysisPassport');
        }

        $info = [
            'url'=>$url,
            'registcapital'=>$res['words_result']['注册资本']['words'],
            'socialcode'=>$res['words_result']['社会信用代码']['words'],
            'name'=>$res['words_result']['单位名称']['words'],
            'legal'=>$res['words_result']['法人']['words'],
            // 'idnumber'=>$res['words_result']['证件编号']['words'],
            // 'construct'=>$res['words_result']['组成形式']['words'],
            'registdate'=>$res['words_result']['成立日期']['words'],
            'address'=>$res['words_result']['地址']['words'],
            // 'range'=>$res['words_result']['经营范围']['words'],
            'type'=>$res['words_result']['类型']['words'],
            'expire'=>$res['words_result']['有效期']['words'],

        ];

        $info['registdate'] = str_replace('日', '',str_replace('月', '-', str_replace('年', '-',$info['registdate'])));
        $info['registcapital'] = ENCRYPT::convertCNtoNumber(str_replace('元', '', $info['registcapital']));

        return $this->take($info)->success('success','companyOCR');

    }

}