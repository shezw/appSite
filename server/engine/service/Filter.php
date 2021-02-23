<?php

/*

name:           filter 字符过滤
version:        1.0.2
author:         Sprite
copyright:      动息科技,DonseeTec
website:        https://donsee.cn
date:           2018.7.16 | 2018.2.19

eg:

*/

namespace APS;

/**
 * 过滤器
 * Filter
 * @package APS\tool
 */
class Filter{

	public static function getValidParam( array $arr = null, string $key = null ){
		if( !isset($arr) || empty($arr) ){ return null; }
		if( isset($arr[$key]) && $arr[$key] ){ return $arr[$key]; }
		return $arr[$key]!=='' ? $arr[$key] : NULL;
	}

	//过滤字符串内不合适内容
	public static function sanitize( $data = null ){

		// 检测必填项
        if( !isset($data) ){ return ASResult::shared(-500,'No data'); }

		if (gettype($data)=='string') {

			return filter_var($data,FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		
		}else if(gettype($data)==='array' || gettype($data)==='object'){

			foreach ($data as $k => $v) {

				if (gettype($v)==='array' || gettype($v)==='object') {
						//递归处理
						
					if (gettype($v)==='array') {
						$data[$k] = Filter::sanitize($v);                   
					}else{
						$data->$k = Filter::sanitize($v);
					}

				}else if(gettype($v)==='string' || gettype($v)==='integer' || gettype($v)==='double' || gettype($v)==='double'){

					if (gettype($v)==='array') {
						$data[$k] = Filter::sanitize($v);                   
					}else{
						$data->$k = filter_var($v,FILTER_SANITIZE_FULL_SPECIAL_CHARS);
					}

				}else{

				    return ASResult::shared(10086,'Input type wrong!',gettype($data),'FILTER-sanitize');
				}
			}
			return $data;

		}else if(gettype($data)==='integer' || gettype($data)==='double' || gettype($data)==='float'){

			return ASResult::shared(0,'Numbers!',$data,'FILTER-sanitize');
		
		}else{
		
			return ASResult::shared(10086,'Input type wrong!',gettype($data),'FILTER-sanitize');

		}
			
	}


	public static function stripslashes_array( array &$array = NULL ) { 
		if(!isset($array)){ return NULL; }
		foreach ($array as $key => $value) {

			if ($key != 'argc' && $key != 'argv' && (strtoupper($key) != $key || ''.intval($key) == "$key")) { 
				if (is_string($value)) { 
					$array[$key] = stripslashes($value); 
				} 
				if (is_array($value)) { 
					$array[$key] = Filter::stripslashes_array($value); 
				} 
			}
		}
		return $array; 
	}

    /**
     * 数组全体过滤
     * addslashesAll
     * @param mixed $array
     * @return array|string
     */
	public static function addslashesAll( $array ){

		if (gettype($array)=='array') {
			foreach ($array as $key => $value) {
				$array[$key] = Filter::addslashesAll($value);
			}
		}elseif(gettype($array)!=='object'){
			return addslashes($array);
		}else{
			return [];
		}

		return $array;
	}



	// 将null等数组转化为 空字符串数组
	public static function spaceInvalid( array $array ){

		foreach ($array as $key => $value) {
			if (gettype($value)=='array') {
				$array[$key]= Filter::spaceInvalid($value);
			}else{
				if ( !isset($value) || $value === NULL || $value ==='' || $value === null || $value === 'NULL' || $value ==='null') {
					$array[$key]='';
				}
			}
		}
		return $array;

	}

	// 将null等数组索引删除
	public static function removeInvalid( array $array ){

		foreach ($array as $key => $value) {
			if(gettype($value)=='array'){
				$array[$key]= Filter::removeInvalid($value);
			}else{
				if ( !isset($value) || $value === NULL || $value ==='' || $value === null || $value === 'NULL' || $value ==='null' ) {
					unset($array[$key]);
				}else{
					$array[$key] = gettype($value)=='string' ? trim($value) : $value;
				}
			}
		}
		return $array;

	}

	/**
     * 从HTML中截取字符串
	 * substrHtml
	 * @param    string                   $str            [输入字符串]
	 * @param    int                      $num            [截取字长]
	 * @param    string|null              $more           [更多字符串]
	 * @return   string                                   [输出字符串]
	 */
	public static function substrHtml( string $str, int $num, string $more = NULL ){

		$len = mb_strlen($str,'utf8');
		
		if($num>=$len){
			return $str;
		}
		
		$word = 0;
		$i    = 0;                     /** 字符串指针 **/
		$stag = array(array());        /** 存放开始HTML的标志 **/
		$etag = array(array());        /** 存放结束HTML的标志 **/
		$sp   = 0;
		$ep   = 0;

		while($word!=$num){

			if(isset($str[$i]) && ord($str[$i])>128){
				//$re.=substr($str,$i,3);
				$i+=3;
				$word++;

			}else if (isset($str[$i]) && $str[$i]=='<'){

					if ($str[$i+1] == '!')
					{
						$i++;
							continue;
					}

					if ($str[$i+1]=='/')    
					{
						$ptag=&$etag ;
						$k=&$ep;
						$i+=2;
					}
					else                    
					{
						$ptag=&$stag;
						$i+=1;
						$k=&$sp;
					}

					for(;$i<$len;$i++)        
					{
						if ($str[$i] == ' ')
						{
							$ptag[$k] = implode('',$ptag[$k]);
							$k++;
							break;
						}
						if ($str[$i] != '>') 
						{
							$ptag[$k][]=$str[$i];
							continue;
						}
						else                
						{
							$ptag[$k] = implode('',$ptag[$k]);
							$k++;
							break;
						}
					}
				$i++;
					continue;
			}else{
				//$re.=substr($str,$i,1);
				$word++;
				$i++;
			
			}
		}

		foreach ($etag as $key => $val){

			$key1 = array_search($val,$stag);
			if ($key1 !== false) unset($stag[$key]);
			
		}

		foreach ($stag as $key => $val){

			if (in_array($val,array('br','img'))) unset($stag[$key1]);
		
		}

		array_reverse($stag);
		$ends = '</'.implode('></',$stag).'>';
		$re   = substr($str,0,$i).$ends;
		$re   = str_replace('<br/>', ' ', $re);
		$re   = str_replace('</br/>', ' ', $re);
		return $re.= $more ?? '' ;

	}


	/**
     * 截取html文字内容
	 * interceptHtmlText
	 * @param    string                   $html           html原文
	 * @param    int|integer              $length         截取长度(中文按1)
	 * @param    string|null              $more           更多字符 (字符会默认占用截取文字空间)
	 * @return   string
	 */
	public static function interceptHtmlText( string $html, int $length = 0, string $more = NULL ){

		$len = $more ? mb_strlen($more) : 0;

        $html = strip_tags($html);

        if($length>0){
            $html = mb_substr($html, 0, $length - $len );
        }
        return $html . ($more ?? '');
	}



	/**
     * 向数组追加数据
	 * supplement
	 * @param    array                    $data             数据
	 * @param    array|null               $supplementData   追加数据
	 * @param    array|null               $keys             指定字段追加
	 * @return   array
	 */
	public static function supplement( array $data, array $supplementData = null , array $keys=null ){

		if (!isset($supplementData) || count($supplementData)==0 ){ return $data; }
		if (!isset($keys)){
			foreach ($supplementData as $k => $v) {
				$data[$k] = $v;
			}
			return $data;
		}

		for ($i=0; $i <count($keys) ; $i++) { 
				
			isset($supplementData[$keys[$i]]) && $data[$keys[$i]] = $supplementData[$keys[$i]];

		}
		return $data;

	}

	/**
     * 净化数组
	 * purify
	 * @param    array|null               $input          输入
	 * @param    array|null               $keys           过滤字段
	 * @param    array|null               $convertStruct  转换格式参考
	 * @return   array                                    输出
	 */
	public static function purify( array $input = null, array $keys = null, array $convertStruct = null ){

		$output = [];

		if( !isset($input) || count($input)===0 ){
			return $output;
		}

		if( !isset($keys) ){
		    return $input;
        }

		foreach ($keys as $k => $v) {
			
			if(gettype($k)=='number'||gettype($k)=='integer'){ // 只提取字段 不关注格式和默认
				if(isset($input[$v]) && $input[$v] !== ''){
                    $output[$v] = $input[$v];
                }
				if(isset($convertStruct) && isset($convertStruct[$v]) && $convertStruct[$v]=='ASjson'){
					$output[$v] = ENCRYPT::ASJsonEncode($input[$v]);
				}
			}else{ // 带有类型和默认值的提取

				if( in_array($v, ENCRYPT::$types) ){ // 只有类型 不含默认值
					
					if( isset($input[$k]) ){ $output[$k] = ENCRYPT::convertValue($v,$input[$k]);}
				
				}else{ // 含有默认值
				
					$type = gettype($v);
					$output[$k] = isset($input[$k]) ? ENCRYPT::convertValue($type,$input[$k]) : $v;
				}
			}
		}
		return $output;
	}

	/**
     * 数组转化为字符串
	 * arrayToString
	 * @param    array|null               $a              数组
	 * @param    string|null              $connection     连接符
	 * @return   string
	 */
	public static function arrayToString( array $a = NULL , string $connection = "," ){

		$s = "";
		if (!$a) {
			return "";
		}
		for ($i=0; $i < count($a); $i++) { 
			$s .= ( $i>0 ? $connection : '' ).$a[$i];
		}
		return $s;
	}


	public static function priceToInt( $price, int $len = 2 ){

		return intval((double)$price * pow(10, $len));

	}

	public static function priceToFloat( $price, int $len = 2 ){

		return (double)(((int)$price)/pow(10, $len));

	}

}

