<?php

/*

name:           encrypt 加密解密
version:        1.0.2
author:         Sprite
copyright:      动息科技,DonseeTec
website:        https://donsee.cn
date:           2018.7.11

eg:
$id   = APS\Encrypt::shortId(24);
$id   = APS\Encrypt::minId(64);
$id   = APS\Encrypt::longId(64);
$id   = APS\Encrypt::radomNum(32);
$sign = APS\Encrypt::sign('appSite');

*/

namespace APS;
/**
 * 加密/字符串处理
 * Encrypt or String
 * @package APS\tool
 */
class Encrypt{

    const TYPES = ['ASJson','JSON','json','INT','int','INTEGER','integer','DOUBLE','double','FLOAT','float','BOOL','BOOLEAN','bool','boolean','NULL','null','STRING','string'];

	public $itoa64;             // @ string     # 字符集
	public $iterationCount;     // @ int        # 迭代深度
	public $portableHashes;     // @ bool       # 
	public $randomState;        // @ string     # 随机蔟
    private $random_state;

    function __construct( $iterationCount = false , $portableHashes = false ){

		$this->itoa64         = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
		$this->iterationCount = $iterationCount ? ( $iterationCount>32 ? 32 : $iterationCount ) : 8 ;
		$this->portableHashes = $portableHashes;
		$this->randomState    = microtime() . uniqid(rand(), TRUE); 

    }

	public static function shortId( int $n=16 ): string
    { //不大于16位

		$n = $n>=16 ? 16 : $n;
		return static::radomCode($n );

	}

	public static function minId( int $n = 64 ): string
    { //不大于64位

		$n = $n>=64 ? 64 : $n;
		return static::radomCode($n);

	}

	public static function longId( int $n = 128 ): string
    { // 不大于128位

		$n = $n>=128 ? 128 : $n;
		return static::radomCode($n);

	}

	public static function randomNumber( int $n = 511 ): string
    { // 不大于512位

		$n = $n>=511 ? 511 : $n;
		$code='';
		for ($i=0; $i <$n ; $i++) { 
			$code .= $i>0 ? rand(0,9) : rand(1,9);
		}
		return $code;
	}

	public static function timeId( $alis=NULL ): string
    {

		return (isset($alis)?$alis:'').date("YmdHis00").static::randomNumber(6);

	}

	public static function radomCode( $n=512 ): string
    { // 不大于512位

        // 检测必填项
		$itoa64  =  '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

		$n = $n >= 512 ? 512 : $n;
		$code='';

		for ($i=0; $i <$n ; $i++) { 
			$code .= $i>0 ? $itoa64[rand(0,61)] : $itoa64[rand(10,61)];
		}
		return $code;
	}

	/**
     * 生成hashID
	 * Generate hashID
	 * @param    mixed                    $params         请求参数
	 * @param    string                   $signal         连接符
	 * @return   string                                   hashID
	 */
	public static function hashID( $params , $signal = ":" ): string
    {

		$hashID = "";

		if(gettype($params)=='array'){

			foreach ($params as $k => $v) {
				$hashID .= $k;
				if(gettype($v)=='array'){
					$hashID .= static::hashID($v,"");
				}else{
					$hashID .= $signal.$v;
				}
			}
		}else{
			$hashID = "MD5:".md5($params);
		}
		return strlen($hashID)<100 ? $hashID : static::dictZip($hashID);
	}

	public static function dictZip( string $input ){
		$dict = [
			'account'=>'a','alias'=>'A','amount'=>'am','apply'=>'ap','area'=>'ar','author'=>'au','avatar'=>'av',
			'bind'=>'b','back'=>'B','balance'=>'bc','birthday'=>'bd',
			'call'=>'c','category'=>'ct','condition'=>'C','content'=>'cn','combine'=>'cb','commerce'=>'cc','cover'=>'cv','createtime'=>'ct',
			'description'=>'d','detail'=>'D',
			'enabled'=>'e','expire'=>'E','email'=>'em',
			'featured'=>'f','field'=>'F','free'=>'fr',
			'group'=>'g','gender'=>'G',
			'item'=>'i','introduce'=>'I',
			'key'=>'k','KEYWORD'=>'K','keyword'=>'kw',
			'lasttime'=>'l','level'=>'L','link'=>'lk',
			'message'=>'m','mobile'=>'M',
			'name'=>'n','notification'=>'N','number'=>'nm',
			'order'=>'o',
			'parentid'=>'p','password'=>'pw','params'=>'pm','payment'=>'py','pocket'=>'pk','point'=>'pt','pay'=>'P','promoter'=>'pr',
			'quantity'=>'q',
			'relation'=>'r','rate'=>'rt','receive'=>'rc','reject'=>'R','reply'=>'ry','refund'=>'rf','request'=>'rq',
			'status'=>'s','sender'=>'S','sort'=>'st','setting'=>'sg','scope'=>'sc','shieldword'=>'sw',
			'table'=>'t','title'=>'Ti','type'=>'T','ticket'=>'tk','time'=>'tm','tradeno'=>'tn',
			'unit'=>'u','user'=>'U',
			'view'=>'v',
			'_'=>'',
		];

		foreach ($dict as $k => $v) {
			$input = str_replace($k, $v, $input);
		}

		return $input;
	}


	public static function LetterToNumber( $letter ): string
    {

		$lt64 = [
			'0'=>'00','1'=>'01','2'=>'02','3'=>'03','4'=>'04','5'=>'05','6'=>'06','7'=>'07','8'=>'08','9'=>'09',
			'A'=>'10','B'=>'11','C'=>'12','D'=>'13','E'=>'14','F'=>'15','G'=>'16','H'=>'17','I'=>'18','J'=>'19',
			'K'=>'20','L'=>'21','M'=>'22','N'=>'23','O'=>'24','P'=>'25','Q'=>'26','R'=>'27','S'=>'28','T'=>'29',
			'U'=>'30','V'=>'31','W'=>'32','X'=>'33','Y'=>'34','Z'=>'35','a'=>'36','b'=>'37','c'=>'38','d'=>'39',
			'e'=>'40','f'=>'41','g'=>'42','h'=>'43','i'=>'44','j'=>'45','k'=>'46','l'=>'47','m'=>'48','n'=>'49',
			'o'=>'50','p'=>'51','q'=>'52','r'=>'53','s'=>'54','t'=>'55','u'=>'56','v'=>'57','w'=>'58','x'=>'59',
			'y'=>'60','z'=>'61'];

		$number = '';

		for ($i=0; $i < strlen($letter); $i++) { 
			$number .= $lt64[$letter[$i]];
		}

		return $number;

	}

	// 马赛克加密方法奇偶位
	public static function intervalMosaic( string $input , bool $odd = true ): string
    {
		$output = '';
		for ( $i = 0; $i < mb_strlen($input); $i+=2) {
			$output .= ($odd?'*':'') . $input[$i] . (!$odd?'*':'');
		}
		return $output;
	}
	
	// 马赛克加密方法左右位
	public static function mirrorMosaic( string $input , bool $left = true ): string
    {
		$output = '';
		$x = 0;
		for ( $i = 0; $i < mb_strlen($input)/2; $i++){
			$output .= $left ? '*' : $input[$i];
			$x ++;
		}
		for ( $j = $x ; $j < mb_strlen($input); $j++){
			$output .= !$left ? '*' : $input[$j];
		}
		return $output;
	}
	
	// 马赛克加密方法中间两边
	public static function middleMosaic( string $input , bool $middle = true ): string
    {
		$output = '';
		$x = 0;
		for ($i = 0; $i < mb_strlen($input)/4; $i++){
			$output .= $middle ? '*' : $input[$i];
			$x ++;
		}
		for ($j = $x ; $j < (mb_strlen($input)*3)/4;$j++){
			$output .= !$middle ? '*' : $input[$j];
			$x ++;
		}
		for ($k = $x ; $k < mb_strlen($input);$k++){
			$output .= $middle ? '*' : $input[$k];
		}
		return $output;
	}

	public static function isNumber( $input ): bool
    {
		return in_array(gettype($input), ['int','integer','float','double']);
	}

    // 为ASJson编码
    public static function ASJsonEncode( $value , bool $sub = false ){

        $validTypes  = ['integer'=>'i','int'=>'i','double'=>'d','bool'=>'b','boolean'=>'b','null'=>'n','NULL'=>'n','string'=>'s'];

        $encode = [];
        $encode['_T']   = $validTypes[gettype($value)] ?? "ASJ";
        $encode['_V']   = [];

        if($encode['_T']=='ASJ'){

            foreach ($value as $k => $v) {

                $encode['_V'][$k] = static::ASJsonEncode($v,true);

            }
        }else{
            $encode['_V'] = $value;
        }

        return $sub ? $encode : json_encode($encode,256);
    }

    // 为ASJson解码
    public static function ASJsonDecode( $ASJson ){

    	if(!isset($ASJson)){ return null;}
        $decode = gettype($ASJson)=='array' ? $ASJson : json_decode(static::convertBreak($ASJson),true);

        if(!is_array($decode)){
        	return $ASJson;
        }

        if(!isset($decode['_T'])){

            foreach ($decode as $key => $value) {
                $decode[$key] = static::ASJsonDecode($value);
            }

            return $decode;
        }

        switch ($decode['_T']) {
            case 'ASJ':
            $v = static::ASJsonDecode($decode['_V']);
            break;
            case 'i':
            $v = (int)$decode['_V'];
            break;
            case 'f':
            case 'd':
            $v = (double)$decode['_V'];
            break;
            case 'b':
            $v = ['true'=>true,'false'=>false,'TRUE'=>true,'FALSE'=>false,'0'=>false,'1'=>true,' '=>false,'NULL'=>false][$decode['_V']] ?? false;
            break;
            case 'n':
            $v = NULL;
            break;
            case 's':
            default:
            $v = $decode['_V'];
            break;
        }

        return $v;

    }

    public static function convertCNtoNumber( string $cnNumber ){

    	if( strstr($cnNumber, '亿') ){

    		$e = explode('亿',$cnNumber);
    		$n = $e[0] * 100000000 + static::convertCNtoNumber($e[1]);

    	}else if( strstr($cnNumber,'万')){

    		$e = explode('万',$cnNumber);
    		$n = $e[0] * 10000;

    	}else{
    		$n = $cnNumber;
    	}
    	// var_dump($e);
    	return $n;
    }

    /**
     * 转化大小到MB,KB,Byte单位
     * convert number size to Byte Size
     * @Author   Sprite                   hello@shezw.com http://donsee.cn
     * @DateTime 2019-09-17T10:34:18+0800
     * @version  1.1
     * @param    int                   $size           比特大小
     * @return   string
     */
    public static function convertByteSize( int $size ): string
    {

        $size = (int)$size;

        if( $size < 1048576 ){

            if( $size < 1024 ){

                return $size." B";

            }else{
                $size = (int)( $size/1024 )." KB";
            }

        }else{
            $size = ( floor($size/10485.76)/100)." MB";
        }

        return $size;
    }


    // 转化数据格式
    public static function convertValue( string $type, $value = NULL ){
    	if(!isset($value)){ return NULL; }
        switch ($type) {
            case 'ASJson':
            case DBField_ASJson:
            $v = static::ASJsonDecode($value);
            break;
            case 'JSON':
            case 'json':
            case DBField_Json:
            $v = json_decode($value,true);
            break;
            case 'INT':
            case 'int':
            case 'INTEGER':
            case 'integer':
            case DBField_Int:
            $v = (int)$value;
            break;
            case 'DOUBLE':
            case 'double':
            case 'FLOAT':
            case 'float':
            $v = (double)$value;
            break;
            case 'BOOL':
            case 'BOOLEAN':
            case 'bool':
            case 'boolean':
            case DBField_Boolean:
            $v = ['true'=>true,'false'=>false,'TRUE'=>true,'FALSE'=>false,'0'=>false,'1'=>true,' '=>false,'NULL'=>false][$value] ?? false;
            break;
            case 'NULL':
            case 'null':
            case DBField_Null:
            $v = NULL;
            break;
            case 'STRING':
            case 'string':
            case DBField_String:
            default:
            $v = $value;
            break;
        }
        return $v;
    }

    public static function convertBreak( string $input){

    	if(strstr($input, "\n")){

    		$input = str_replace("\t", "\\t", $input);
    		$input = str_replace("\r", "\\r", $input);
    		$input = str_replace("\n", "\\n", $input);
    	}

		return $input;
   
    }

	public static function sign( $name=false ): string
    {

		$t    = time();
		$salt = uniqid(rand(), TRUE);
		$sign = array(
			'name'=>$name,
			'salt'=>$salt,
			'time'=>$t
		);
		$sign = json_encode($sign);

		return base64_encode($sign);
	}

	public function getRandomBytes($count)
	{
		$output = '';
		if ( @is_readable('/dev/urandom') &&
		    ($fh = @fopen('/dev/urandom', 'rb'))) {
			$output = fread($fh, $count);
			fclose($fh);
		}

		if (strlen($output) < $count) {
			$output = '';
			for ($i = 0; $i < $count; $i += 16) {
				$this->random_state =
				    md5(microtime() . $this->random_state);
				$output .=
				    pack('H*', md5($this->random_state));
			}
			$output = substr($output, 0, $count);
		}

		return $output;
	}

	public function encode64($input, $count): string
    {
		$output = '';
		$i = 0;
		do {
			$value = ord($input[$i++]);
			$output .= $this->itoa64[$value & 0x3f];
			if ($i < $count)
				$value |= ord($input[$i]) << 8;
			$output .= $this->itoa64[($value >> 6) & 0x3f];
			if ($i++ >= $count)
				break;
			if ($i < $count)
				$value |= ord($input[$i]) << 16;
			$output .= $this->itoa64[($value >> 12) & 0x3f];
			if ($i++ >= $count)
				break;
			$output .= $this->itoa64[($value >> 18) & 0x3f];
		} while ($i < $count);

		return $output;
	}

	public function gensaltPrivate($input): string
    {
		$output = '$P$';
		$output .= $this->itoa64[min($this->iterationCount +
			((PHP_VERSION >= '5') ? 5 : 3), 30)];
		$output .= $this->encode64($input, 6);

		return $output;
	}

	public function cryptPrivate($password, $setting): string
    {
		$output = '*0';
		if (substr($setting, 0, 2) == $output)
			$output = '*1';

		$id = substr($setting, 0, 3);
		# We use "$P$", phpBB3 uses "$H$" for the same thing
		if ($id != '$P$' && $id != '$H$')
			return $output;

		$count_log2 = strpos($this->itoa64, $setting[3]);
		if ($count_log2 < 7 || $count_log2 > 30)
			return $output;

		$count = 1 << $count_log2;

		$salt = substr($setting, 4, 8);
		if (strlen($salt) != 8)
			return $output;

		# We're kind of forced to use MD5 here since it's the only
		# cryptographic primitive available in all versions of PHP
		# currently in use.  To implement our own low-level crypto
		# in PHP would result in much worse performance and
		# consequently in lower iteration counts and hashes that are
		# quicker to crack (by non-PHP code).
		if (PHP_VERSION >= '5') {
			$hash = md5($salt . $password, TRUE);
			do {
				$hash = md5($hash . $password, TRUE);
			} while (--$count);
		} else {
			$hash = pack('H*', md5($salt . $password));
			do {
				$hash = pack('H*', md5($hash . $password));
			} while (--$count);
		}

		$output = substr($setting, 0, 12);
		$output .= $this->encode64($hash, 16);

		return $output;
	}

	public function gensaltExtended($input): string
    {
		$count_log2 = min($this->iterationCount + 8, 24);
		# This should be odd to not reveal weak DES keys, and the
		# maximum valid value is (2**24 - 1) which is odd anyway.
		$count = (1 << $count_log2) - 1;

		$output = '_';
		$output .= $this->itoa64[$count & 0x3f];
		$output .= $this->itoa64[($count >> 6) & 0x3f];
		$output .= $this->itoa64[($count >> 12) & 0x3f];
		$output .= $this->itoa64[($count >> 18) & 0x3f];

		$output .= $this->encode64($input, 3);

		return $output;
	}

	public function gensaltBlowfish($input): string
    {
		# This one needs to use a different order of characters and a
		# different encoding scheme from the one in encode64() above.
		# We care because the last character in our encoded string will
		# only represent 2 bits.  While two known implementations of
		# bcrypt will happily accept and correct a salt string which
		# has the 4 unused bits set to non-zero, we do not want to take
		# chances and we also do not want to waste an additional byte
		# of entropy.
		$itoa64 = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

		$output = '$2a$';
		$output .= chr(ord('0') + $this->iterationCount / 10);
		$output .= chr(ord('0') + $this->iterationCount % 10);
		$output .= '$';

		$i = 0;
		do {
			$c1 = ord($input[$i++]);
			$output .= $itoa64[$c1 >> 2];
			$c1 = ($c1 & 0x03) << 4;
			if ($i >= 16) {
				$output .= $itoa64[$c1];
				break;
			}

			$c2 = ord($input[$i++]);
			$c1 |= $c2 >> 4;
			$output .= $itoa64[$c1];
			$c1 = ($c2 & 0x0f) << 2;

			$c2 = ord($input[$i++]);
			$c1 |= $c2 >> 6;
			$output .= $itoa64[$c1];
			$output .= $itoa64[$c2 & 0x3f];
		} while (1);

		return $output;
	}

	public function hashPassword($password): string
    {
		if ( strlen( $password ) > 4096 ) {
			return '*';
		}

		$random = '';

		if (CRYPT_BLOWFISH == 1 && !$this->portableHashes) {
			$random = $this->getRandomBytes(16);
			$hash =
			    crypt($password, $this->gensaltBlowfish($random));
			if (strlen($hash) == 60)
				return $hash;
		}

		if (CRYPT_EXT_DES == 1 && !$this->portableHashes) {
			if (strlen($random) < 3)
				$random = $this->getRandomBytes(3);
			$hash =
			    crypt($password, $this->gensaltExtended($random));
			if (strlen($hash) == 20)
				return $hash;
		}

		if (strlen($random) < 6)
			$random = $this->getRandomBytes(6);
		$hash =
		    $this->cryptPrivate($password,
		    $this->gensaltPrivate($random));
		if (strlen($hash) == 34)
			return $hash;

		# Returning '*' on error is safe here, but would _not_ be safe
		# in a crypt(3)-like public function used _both_ for generating new
		# hashes and for validating passwords against existing hashes.
		return '*';
	}

	public function checkPassword($password, $storedHash): bool
    {

		if ( strlen( $password ) > 4096 ) {
			return false;
		}

		$hash = $this->cryptPrivate($password, $storedHash);

        if ($hash[0] == '*'){
			$hash = crypt($password, $storedHash);
        }

        return $hash === $storedHash;
	}

}
