<?php

/*

name:           file 文件操作
version:        1.0.0
author:         Sprite
copyright:      动息科技,DonseeTec
website:        https://donsee.cn
date:           2018.2.2

*/

namespace APS;

/**
 * 文件操作
 * File Management
 * @package APS\tool
 * @example
    APS\static::addDir('a/1/2/3');                   addDir       # 建立文件夹
    APS\static::addFile('b/1/2/3');                  addFile      # 建立文件
    APS\static::addFile('b/1/2/3.md');               addFile      # 建立文件
    APS\static::copyDir('b','d/e');                  copyDir      # 复制文件夹
    APS\static::copyFile('b/1/2/3.md','b/b/3.md');   copyFile     # 复制文件
    APS\static::moveDir('a/','b/c');                 moveDir      # 移动文件夹
    APS\static::moveFile('b/1/2/3.md','b/d/3.md');   moveFile     # 移动文件
    APS\static::removeFile('b/d/3.md');              removeFile   # 删除文件
    APS\static::removeDir('d');                      removeDir    # 删除文件夹
 */
class File{

    /**
     * 写入文件
     * write
     * @param  String       $filename
     * @param  mixed        $content
     * @param  String|null  $path
     * @param  String       $mode
     * @return bool
     */
    public static function write( String $filename, $content, String $path = null, String $mode = 'a' ): bool
    {

        $fileUrl = $path.$filename;

        if(!file_exists($fileUrl)){
            static::addFile($fileUrl);
        }

        $file = fopen($fileUrl, $mode) or die('No File Access.');

        fwrite($file, $content);
        fclose($file);

        return file_exists($fileUrl);
    }

    public static function addTo( string $filename, $content, string $path = null ):bool
    {
        return static::write( $filename, $content,$path,'a' );
    }


    /**
     * 建立文件( 默认追加 )
     * Create a file or append to old one
     * @param string $fullPath
     * @param boolean $overWrite 该参数控制是否覆盖原文件
     * @return boolean
     */
    public static function addFile(string $fullPath, $overWrite = false): bool
    {

        if (file_exists($fullPath) && $overWrite == false) {
            return true;
        } else if (file_exists($fullPath) && $overWrite == true) {
            static::removeFile($fullPath);
        }
        $fullDirectory = dirname($fullPath);

        static::addDir($fullDirectory);
        return touch($fullPath);
    }

    /**
     * 新建文件(已存在会被覆盖)
     * create a file or rewrite the old one
     * @param  string       $filename
     * @param  mixed        $content
     * @param  string|null  $path
     * @return bool
     */
    public static function newFile(String $filename, $content, string $path = null ): bool
    {
        return static::write($filename,$content,$path,'wb');
    }

    /**
     * 根据结构生成文件夹
     * generate directories by struct
     * @param  array   $struct
     * @param  string  $path
     */
    public static function makeDirs( array $struct, string $path = '' ){

        # need rewrite
    }

    /**
     * 根据结构生成目录
     * dirList
     * @param    array                    $struct         [description]
     * @param    string                   $path           [description]
     * @return   array
     */
    public static function dirList( array $struct, string $path = ''): array
    {

        # need rewrite
    }

    /**
     * 建立文件夹
     * @param string $fullPath
     * @return bool
     */
    public static function addDir(string $fullPath): bool
    {

        if (!file_exists($fullPath)) {
            return mkdir($fullPath,0777,true);
        }
        return true;
    }

    /**
     * 移动文件夹
     *
     * @param string $oldDir
     * @param string $fullDirectory
     * @param boolean $overWrite 该参数控制是否覆盖原文件
     * @return boolean
     */
    public static function moveDir(string $oldDir, string $fullDirectory, $overWrite = false): bool
    {
        # need rewrite with rename
    }

    /**
     * 移动文件
     *
     * @param string $from
     * @param string $toFullPath
     * @param boolean $overWrite 该参数控制是否覆盖原文件
     * @return boolean
     */
    public static function moveFile(string $from, string $toFullPath, $overWrite = false): bool
    {
        if (!file_exists($from)) {
            return false;
        }
        if (file_exists($toFullPath) && !$overWrite) {
            return true;
        } elseif (file_exists($toFullPath) && $overWrite) {
            static::removeFile($toFullPath);
        }
        $fullDirectory = dirname($toFullPath);
        static::addDir($fullDirectory);
        return rename($from, $toFullPath);
    }

    /**
     * 删除文件夹
     *
     * @param string $fullDirectory
     * @return boolean
     */
    public static function removeDir( string $fullDirectory ): bool
    {

        return rmdir($fullDirectory);
    }

    /**
     * 删除文件
     *
     * @param string $fullPath
     * @return boolean
     */
    public static function removeFile( string $fullPath ): bool
    {
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        } else {
            return true;
        }
    }

    /**
     * 复制文件夹
     * @param string $oldDir
     * @param string $fullDirectory
     * @param boolean $overWrite 该参数控制是否覆盖原文件
     * @return void
     */
    public static function copyDir(string $oldDir, string $fullDirectory, $overWrite = false) {
        # need rewrite with rename
    }

    /**
     * 复制文件
     *
     * @param string $from
     * @param string $toFullPath
     * @param boolean $overWrite 该参数控制是否覆盖原文件
     * @return boolean
     */
    public static function copyFile(string $from, string $toFullPath, $overWrite = false): bool
    {
        if (!file_exists($from)) {
            return false;
        }
        if (file_exists($toFullPath) && !$overWrite ) {
            return false;
        } elseif (file_exists($toFullPath) && $overWrite ) {
            static::removeFile($toFullPath);
        }
        $fullDirectory = dirname($toFullPath);
        static::addDir($fullDirectory);
        return copy($from, $toFullPath);
    }

}