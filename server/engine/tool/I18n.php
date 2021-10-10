<?php

/**

name:           Internationalization 国际化
version:        2.0.0
author:         Sprite
copyright:      动息科技,DonseeTec
website:        https://donsee.cn
date:           2018.4.28,2020.03.22

*/

namespace APS;

/**
 * 本地化
 * I18n
 * 默认加载 en-WW.lang 语言包

    ! 需要Redis支持， 未安装/配置Redis高并发时会遇到CPU/内存瓶颈
    ! Requires Redis support, CPU/memory bottlenecks will be encountered when Redis is not installed / configured with high concurrency

 * @package APS\tool
 */
class I18n{

	/**
	 * 语言包字典
	 * @var array
	 */
	private $dictionary;

    /**
     * 语言包已加载
     * @var bool
     */
    private $dictionaryLoaded = false;

    /**
     * 缓存数据库
     * @var ASRedis
     */
	private $Redis;

    /**
     * 缓存数据库已启用
     * @var bool
     */
    private $RedisEnabled = false;

	/**
	 * 当前语言
	 * @var string
	 */
	private $lang = "en-WW";

    /**
     * 自定义路径
     * @var array
     */
	private $customDirectories = [];

	function __construct( string $lang = null ){

        $this->lang = $lang ?? $this->checkClientLanguage() ?? CONFIG['DEFAULT_LANG'] ?? 'en-WW' ;
        $this->dictionary = [];
        $this->checkDictionary();
	}

    /**
     * 单例
     * @param    String $lang
     * @return   I18n
     */
	public static function shared( string $lang = null ):I18n{

        if ( !isset($GLOBALS['I18n']) ){
            $GLOBALS['I18n'] = new I18n($lang);
        }
        return $GLOBALS['I18n'];

	}

    protected function getRedis():ASRedis{
        if( !isset($this->Redis) ){
            $this->Redis = _ASRedis();
        }
        return $this->Redis;
    }


    /**
     * 设置本地化语言
     * setLang
     * @param  String  $lang
     * @param  bool    $forceRefresh  强制刷新语言包
     */
	public function setLang( String $lang , bool $forceRefresh = false ) {
	    $this->lang = $lang;

        $this->loadDictionary($forceRefresh);
        if( !empty($this->customDirectories) ){
            foreach ( $this->customDirectories as $i => $directory ){
                $this->supplementWith($directory,$forceRefresh);
            }
        }
    }

    /**
     * 检查语言包是否加载
     * checkDictionary
     */
	public function checkDictionary() {
        if( !$this->dictionaryLoaded ){
            $this->loadDictionary();
        }
    }

    /**
     * 加载语言包
     * loadDictionary
     * @param bool $refresh 强制刷新
     */
    public function loadDictionary( bool $refresh = false ){

        if( !$this->getRedis()->isEnabled() ){
            _ASError()->add( ASResult::shared(606,"Redis is not enabled",null,'I18N->loadDictionary') );
            $this->dictionary = include ( __DIR__."/localization/" )."{$this->lang}.lang";
            $this->dictionaryLoaded = true;

        }else{

            if (!$this->getRedis()->has("i18n_{$this->lang}") || $refresh ) {

                $this->dictionary = include ( __DIR__."/localization/" )."{$this->lang}.lang";
                $this->cacheToRedis( "i18n_{$this->lang}" );
            }
            $this->dictionaryLoaded = true;
            $this->RedisEnabled = true;
        }
    }


    /**
     * 通过路径自动加载额外本地化文件
     * supplementWith
     * @param  string  $directory
     * @param  bool    $refresh
     */
    public function supplementWith( string $directory, bool $refresh = false ){

        $fileID = "i18n_{$directory}_{$this->lang}";
        if( !in_array( $directory,$this->customDirectories ) ){
            $this->customDirectories[] = $directory;
        }
        if( $this->RedisEnabled && $this->getRedis()->has($fileID) && !$refresh ){
            return ;
        }

        $filePath = $directory."{$this->lang}.lang";
        if( file_exists( $filePath ) ){

            $supplementDict = include( $filePath );

            $this->dictionary = array_merge_recursive( $this->dictionary, $supplementDict );

            if( $this->RedisEnabled ){

                $this->cacheToRedis( $fileID );
            }
        }
    }

    /**
     * 缓存到Redis服务器
     * cacheToRedis
     * @param  string  $fileID
     */
    private function cacheToRedis( string $fileID ){

        foreach ( $this->dictionary as $key => $value ){

            $this->getRedis()->cache( "i18n_{$this->lang}_".$key,$value, Time::WEEK );
        }
        $this->getRedis()->cache($fileID,true,Time::WEEK);

    }


    /**
     * 进行文本的本地化处理
     * Translate text with code
     * @param  String $code
     * @return String
     */
	public function translate( String $code ):string{
	    if( $this->RedisEnabled ){
            $getFromRedis = $this->getRedis()->read("i18n_{$this->lang}_".$code);
            if( $getFromRedis->isSucceed() ){
                return $getFromRedis->getContent() ?? "{$code}";
            }
        }
//	    $this->loadDictionary(true);
	    return $this->dictionary[$code] ?? "{$code}";
    }

    /**
     * 对内容进行域内转码
     * Transcode in scope
     * @param  String $code
     * @param  String $inScope
     * @return String
     */
    public function transcoding( String $code , String $inScope ):string{

        if( $this->RedisEnabled ){
            $getFromRedis = $this->getRedis()->read("i18n_{$this->lang}_".$inScope);
            return $getFromRedis->isSucceed() ? $getFromRedis->getContent()[$code] ?? "{$inScope}.{$code}" : "{$inScope}.{$code}";
        }
        return $this->dictionary[$inScope][$code] ?? "{$inScope}.{$code}";
    }


    /**
     * 检测客户端本地化语言
     * checkClientLanguage
     * @return string|null
     */
    public function checkClientLanguage() {

        $lang = explode(',',$_SERVER['HTTP_ACCEPT_LANGUAGE'])[0];

        if( file_exists(__DIR__.'/localization/'.$lang.'.lang') ){
            return $lang;
        }
        return null;
    }


    /**
     * 当前语言
     * currentLang
     * @return string
     */
    public function currentLang():string{
        return $this->lang;
    }

}