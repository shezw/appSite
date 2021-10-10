<?php
/**
 * @name:           time 时间
 * @version:        2.0.0
 * @date:           2018.4.28,2020.3.15
 * @mark:
 *      ini_set('date.timezone','Asia/Shanghai');
 *      date_default_timezone_set('PRC');
 */

namespace APS;

use DateTime;

/**
 * 时间
 * Time
 * @package APS\tool
 */
class Time{

    const  CENTURY     =  3155692600;# 百年
	const  YEAR        =  31556926;  # 一年
	const  THIRTY      =  2592000;   # 30天
	const  WEEK        =  604800;    # 一周
	const  DAY         =  86400;     # 一天
	const  HOUR        =  3600;      # 一小时
	const  HALFHOUR    =  1800;      # 半小时
	const  MIN         =  60;        # 一分钟
	const  SECOND      =  1;         # 一秒

	public $time;          # 主时间
	public $now;           # 现在时间 时间戳

//	public $lang;          # 语言 i18n Code  Default by en-WW

	function __construct( int $specifiedTime = null )
	{

        $this->now       = microtime(true);
		$this->time      = $specifiedTime ?? $this->now;
	}

    /**
     * 单例
     * common
     * @param  int|null  $specifiedTime
     * @return Time
     */
	public static function common(int $specifiedTime = null):Time{
	    return new static($specifiedTime);
    }

    /**
     * 当前时间单例
     * @return Time
     */
    public static function now():Time{
        return new static();
    }

    /**
     * 从字符串建立Time
     * Init by String time
     * 中文格式: ?年?月?日 ?时?分
     * 英文格式: Y-m-d H:s
     * @param String $time
     * @return Time
     */
    public static function fromString( string $time ):Time{

        if( strstr( $time, '日' ) ){

            $time = str_replace("年", "-", $time);
            $time = str_replace("月", "-", $time);
            $time = str_replace("日", " ", $time);

            $time = str_replace("时", ":", $time);
            $time = str_replace("分", "", $time);
        }

        return new static( strtotime($time) );
    }

    /**
     * 获取时间是上午还是下午
     * @return string
     */
    public function AmOrPm():string{

        return (int)date("H",$this->time ) < 12 ? "AM" : "PM";
    }

    /**
     * 获取当天开始
     * The beginning of current day
     * @return Time
     */
	public function thisDay():Time{
	    return new static( strtotime(date('Y-m-d',$this->time)) );
    }

    /**
     * 获取到当天时间戳
     * The beginning timestamp of current day
     * @return int
     */
    public function today():int{
	    return strtotime(date("Y-m-d"),$this->time );
    }

    /**
     * 获取前一天
     * The beginning of last day (yesterday)
     * @return Time
     */
    public function lastDay():Time{
	    return new static($this->thisDay()->time - static::DAY);
    }

    /**
     * 获取前一天时间戳
     * The beginning timestamp of yesterday
     * @return int
     */
    public function yesterday():int{
	    return $this->thisDay()->time - static::DAY;
    }

    /**
     * 获取后一天
     * The beginning of next day (tomorrow)
     * @return Time
     */
    public function nextDay():Time{
	    return new Time( $this->thisDay()->time + static::DAY );
    }

    /**
     * 获取后一天时间戳
     * The beginning timestamp of next day
     * @return int
     */
    public function tomorrow():int{
	    return $this->thisDay()->time + static::DAY;
    }

    /**
     * 获取当月开始
     * The beginning of current month
     * @return Time
     */
    public function thisMonth():Time{
        return new static( strtotime(date('Y-m-01',$this->time)) );
    }

    /**
     * 获取上个月的开始时间
     * The beginning of last month
     * @return Time
     */
    public function lastMonth():Time{

        $year  = (int)date("Y",$this->time);
        $month = (int)date("m",$this->time);

        $year  = $month>1 ? $year : $year-1;
        $month = $month>1 ? $month-1 : 12;
        return new static( strtotime("{$year}-{$month}-01") );
    }

    /**
     * 获取下个月开始时间
     * The beginning of next month
     * @return Time
     */
    public function nextMonth():Time{

        $year  = (int)date("Y",$this->time);
        $month = (int)date("m",$this->time);

        $year  = $month<12 ? $year : $year+1;
        $month = $month<12 ? $month+1 : 1;

        return new static( strtotime("{$year}-{$month}-01") );
    }

    /**
     * 当前年度开端
     * The beginning of current year
     * @return Time
     */
    public function thisYear():Time{
        return new static( strtotime(date('Y-01-01',$this->time)) );
    }

    /**
     * 下一年度开端
     * The beginning of next year
     * @return Time
     */
    public function nextYear():Time{
        $year = (int)date("Y",$this->time) + 1;
        return new static(strtotime("{$year}-01-01"));
    }

    /**
     * 获取对应的周几
     * @return int
     */
    public function weekday():int{
        $w = date( 'w',$this->time );
        return [7,1,2,3,4,5,6][ $w ];
    }

    /**
     * 获取对应的日期
     * @return int
     */
    public function date():int{
        return date( 'd',$this->time );
    }

    /**
     * 获取对应的月份
     * @return int
     */
    public function month():int{
        return date( 'm',$this->time );
    }

    /**
     * 获取对应的年份
     * @return int
     */
    public function year():int{
        return date( 'Y',$this->time );
    }

    /**
     * 检测是否昨天
     * @return bool
     */
	public function isYesterday():bool{

        $todayTimestamp = static::now()->today();
		return $this->time < $todayTimestamp && $this->time > $todayTimestamp - static::DAY ;
	}

    /**
     * 是否昨天之前
     * @return bool
     */
	public function isBeforeToday( ):bool{

		return $this->time < static::now()->today() ;
	}

    /**
     * 是否N天之前
     * @param int $countOfDays
     * @return bool
     */
	public function isBeforeDays( int $countOfDays ):bool{

		return $this->time < static::now()->today() - $countOfDays * static::DAY;
	}

    /**
     * 根据预设格式输出
     * Output by preset format
     * @param String $format
     * @return String
     */
	public function formatOutput( string $format = TimeFormat_LiteTime ):string{

		return date(i18n($format,'TimeFormat'),$this->time );
	}


    /**
     * 特定格式输出
     * Output by Specific format
     * @param String $StringFormat
     * @param int|null $timeStamp
     * @return String
     */
	public function customOutput( string $StringFormat = "Y-m-d H:s", int $timeStamp = null ):string{

		return date($StringFormat,$timeStamp ?? $this->time );
    }


    /**
     * 人性化输出
     * User-friendly output
     * @return string
     */
	public function humanityOutput(  ):string{

	    $lang =  _I18n()->currentLang();
		$instant   = microtime(true);
		$duration  = $this->time - $instant;

		if ( $duration > 100 * static::YEAR ){
			
			return ['zh-CN'=>"长期",'en-WW'=>'Long time'][$lang];
		
		}else if ($duration + $this->now > $this->nextyear()->time ){ // 今年以后   n年n月n日
			
			return date(i18n(TimeFormat_FullDate,'TimeFormat'),$this->time);
		
		}else if ($duration>7*static::DAY)         { // 7天到1年   n年n月n日
			
			return date(i18n(TimeFormat_LiteDate,'TimeFormat'),$this->time);
		
		}else if ( $duration > static::DAY ) { // 1天到7天  n天后

			$c = intval($duration / static::DAY );
			return [
				'zh-CN'=>$c."天后",
				'en-WW'=> "After $c day".($c==1 ? "" : "s")
			][$lang];
		
		}else if ($duration > static::HOUR)   { // 一天以内  n小时后
			
			$c = intval($duration/static::HOUR);
			return [
				'zh-CN'=>$c."小时后",
				'en-WW'=> "After $c hour".($c==1 ? "" : "s")
			][$lang];
				
		}else if ($duration > static::MIN)    { // 一小时以内 n分钟后
			
			$c = intval($duration/static::MIN);
			return [
				'zh-CN'=>$c."分钟后",
				'en-WW'=> "After $c minute".($c==1 ? "" : "s")
			][$lang];
		
		}else if ($duration > static::SECOND) { // 一分钟以内 n秒后
			
			$c = intval($duration/static::SECOND);
			return [
				'zh-CN'=>$c."秒后",
				'en-WW'=> "After $c second".($c==1 ? "" : "s")
			][$lang];
					
		}else if ($duration < -100*static::YEAR)   { // 100年以前   n年n月n日
			
			return ['zh-CN'=>'很久以前','en-WW'=>'long time before'][$lang];
		
		}else if ($duration < ( $this->thisyear()->time - $instant) )   { // 今年之前   n年n月n日
			
			return date(i18n(TimeFormat_FullDate,'TimeFormat'),$this->time);
		
		}else if ( $duration < -7*static::DAY ) { // 7天以前   n年n月n日
			
			return date(i18n(TimeFormat_LiteDate,'TimeFormat'),$this->time);
		
		}else if ( $duration < -static::DAY ) { // 一天以前  n天前
			
			$c = intval($duration/-static::DAY);
			return [
				'zh-CN'=>$c."天前",
				'en-WW'=> "Before $c day".($c==1 ? "" : "s")
			][$lang];
					
		}else if ($duration<-static::HOUR)  { // 一小时前  n小时前
			
			$c = intval($duration/-static::HOUR);
			return [
				'zh-CN'=>$c."小时前",
				'en-WW'=> "Before $c hour".($c==1 ? "" : "s")
			][$lang];
					
		}else if ($duration<-static::MIN)   { // 一分钟前  n分钟前
			
			$c = intval($duration/-static::MIN);
			return [
				'zh-CN'=>$c."分钟前",
				'en-WW'=> "Before $c minute".($c==1 ? "" : "s")
			][$lang];
					
		}else if ($duration<-static::SECOND){ // 一秒前    n秒前
			
			$c = intval($duration/-static::SECOND);
			return [
				'zh-CN'=>$c."秒前",
				'en-WW'=> "Before $c second".($c==1 ? "" : "s")
			][$lang];
					
		}else if ( $this->time > 0 ){
			
			return ['zh-CN'=>"刚刚",'en-WW'=>'Just now'][$lang];
		
		}else{
			
			return ['zh-CN'=>"时空裂隙,无从得知",'en-WW'=>'Mysterious'][$lang];
		
		}
	}

    /**
     * 输出ISO8601格式
     * @param $timeStamp
     * @return String
     */
	public static function ISO8601( int $timeStamp ):string {
		$dtStr      = date("c", $timeStamp);
        try {
            $dateTime = new DateTime($dtStr);
            $expiration = $dateTime->format(DateTime::ISO8601);
            $pos        = strpos($expiration, '+');
            $expiration = substr($expiration, 0, $pos);
            return $expiration."Z";
        } catch (\Exception $e) {
            return "";
        }
	}

    /**
     * 将数字时长转换为文字输出
     * @param int $duration
     * @param string $format ( With Mixer syntax )
     * @return string
     */
	public static function durationToString( int $duration , string $format = '{{year}}-{{month}}-{{day}} {{hour}}:{{minute}} {{second}}' ):string{

		$y = (int)($duration / static::YEAR);
		$duration -= $y * static::YEAR;
		$d = (int)($duration / static::DAY);
		$duration -= $d * static::DAY;
		$h = (int)($duration / static::HOUR);
		$duration -= $h * static::HOUR;
		$m = (int)($duration / static::MIN);
		$duration -= $m * static::MIN;
		$s = (int)($duration / static::SECOND);

		$string  = "";
		$string .= ($y?"{$y}年":'');
		$string .= ($d?"{$d}天":'');
		$string .= ($h?"{$h}小时":'');
		$string .= ($m?"{$m}分钟":'');
		$string .= ($s?"{$s}秒":'');

        return $string;

	}

    /**
     * 将字符串时分转换为秒数
     * Convert string hours and minutes to seconds duration
     * @param $timeString
     * @return int
     */
    public static function hoursToDuration( string $timeString ):int{

        $t = explode(':', $timeString);
        $s = ((int)$t[0])*3600+((int)$t[1])*60;

        return $s;
    }

    /**
     * 获取标准13位时间戳
     * @return float
     */
    public static function getMillisecond(): float
    {

        list($t1, $t2) = explode(' ', microtime());

        return (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);

    }

}


