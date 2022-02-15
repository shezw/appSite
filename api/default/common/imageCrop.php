<?php
/**
 * Description
 * OSSCallback.php
 */

namespace common;

use APS\ASAPI;
use APS\ASResult;
use APS\ImageUtil;

class imageCrop extends ASAPI
{
    const scope = ASAPI_Scope_Public;

    const cropMethods =
'{
    "cover":{"crop":true,"w":450,"h":225,"q":90},
    "thumb":{"crop":true,"w":480,"h":360,"q":90},
    "coverl":{"crop":true,"w":600,"h":300,"q":90},
    "coverm":{"crop":true,"w":450,"h":225,"q":80},
    "covers":{"crop":true,"w":300,"h":150,"q":65},
    "thumbl":{"crop":true,"w":600,"h":375,"q":90},
    "thumbm":{"crop":true,"w":480,"h":360,"q":80},
    "thumbs":{"crop":true,"w":300,"h":225,"q":65},
    "coverxl":{"crop":true,"w":900,"h":450,"q":90},
    "coverxs":{"crop":true,"w":200,"h":100,"q":65},
    "thumbxl":{"crop":true,"w":900,"h":675,"q":90},
    "thumbxs":{"crop":true,"w":200,"h":150,"q":65},
    "coverxxl":{"crop":true,"w":1200,"h":600,"q":90},
    "thumbxxl":{"crop":true,"w":1200,"h":750,"q":90},
    "l":{"crop":false,"w":1250,"q":90},
    "m":{"crop":false,"w":750,"q":80},
    "s":{"crop":false,"w":500,"q":65},
    "xl":{"crop":false,"w":1500,"q":90},
    "xs":{"crop":false,"w":250,"q":65},
    "xxs":{"crop":false,"w":125,"q":65},
    "avatar":{"crop":true,"w":450,"h":450,"q":90},
    "banner":{"crop":true,"w":450,"h":180,"q":90},
    "mobile":{"crop":true,"w":270,"h":480,"q":90},
    "avatarl":{"crop":true,"w":600,"h":600,"q":90},
    "avatarm":{"crop":true,"w":450,"h":450,"q":80},
    "avatars":{"crop":true,"w":300,"h":300,"q":65},
    "bannerl":{"crop":true,"w":600,"h":240,"q":90},
    "bannerm":{"crop":true,"w":450,"h":180,"q":80},
    "banners":{"crop":true,"w":300,"h":120,"q":65},
    "mobilel":{"crop":true,"w":338,"h":600,"q":90},
    "mobilem":{"crop":true,"w":270,"h":480,"q":80},
    "mobiles":{"crop":true,"w":168,"h":300,"q":65},
    "avatarxl":{"crop":true,"w":900,"h":900,"q":90},
    "avatarxs":{"crop":true,"w":200,"h":200,"q":65},
    "bannerxl":{"crop":true,"w":900,"h":360,"q":90},
    "bannerxs":{"crop":true,"w":200,"h":80,"q":65},
    "mobilexl":{"crop":true,"w":506,"h":900,"q":90},
    "mobilexs":{"crop":true,"w":112,"h":200,"q":65},
    "avatarxxl":{"crop":true,"w":1200,"h":1200,"q":90},
    "bannerxxl":{"crop":true,"w":1200,"h":480,"q":90},
    "mobilexxl":{"crop":true,"w":675,"h":1200,"q":90},
    "xxl":{"crop":false,"w":2000,"q":90},
    "post":{"crop":true,"w":360,"h":480,"q":90},
    "postl":{"crop":true,"w":450,"h":600,"q":90},
    "postm":{"crop":true,"w":360,"h":480,"q":80},
    "posts":{"crop":true,"w":225,"h":300,"q":65},
    "postxl":{"crop":true,"w":675,"h":900,"q":90},
    "postxs":{"crop":true,"w":150,"h":200,"q":65},
    "postxxl":{"crop":true,"w":900,"h":1200,"q":90},
    "tbanner":{"crop":true,"w":1920,"h":480,"q":90}
}';

    /**
     * @description  The final resource name like "/dir/image_(cover).jpg"

     * @param string file
     * @param string ext
     * @param string method  @see static::cropMethods
     *
     * @return ASResult
     */

    public function run(): ASResult
    {

        $ext    = $this->params['ext'];
        $method = $this->params['method'];
        $file   = $this->params['file'];

        $originFile = str_replace( getConfig('STATIC_PATH'), STATIC_DIR, $file );

        $cropFile   = str_replace( $ext, "/crop/{$method}{$ext}", $originFile );

        $cropFilePath  = str_replace( $ext, "/crop/{$method}{$ext}", $file );

        $type = str_replace('.','',$ext);

        $methods = json_decode( static::cropMethods, true );

        $cropMethod = $methods[ $method ];

        if( !$cropMethod ){
            return $this->error(510,'Not Valid Method.' );
        }

        $imageUtil = new ImageUtil();

        if( $cropMethod['crop'] ){

            $imageUtil->crop( $originFile, $cropMethod['w'], $cropMethod['h'] );

        }else{

            $imageUtil->zoomTo( $originFile, $cropMethod['w'], $cropMethod['h'] );
        }

        if ( $imageUtil->save( $cropFile, $type, $cropMethod['q'] ) ){

            header("location:{$cropFilePath}");
        }

        return $this->success();
    }


}