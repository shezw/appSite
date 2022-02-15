<?php

namespace APS;

use Gregwar\Image\Image;

require_once LIB_DIR."phpImage/autoload.php";

class ImageUtil {

    private $util;

    public function __construct()
    {
        $this->util = new Image();
    }

    public function crop( string $file, float $w, float $h ){

        $this->util->fromFile($file)
            ->zoomCrop( $w, $h, 0xffffff , 'center' ,'center' )

            ;
    }


    public function zoomTo( string $file, float $w = null, float $h = null ){

        $this->util->fromFile($file)
            ->scaleResize( $w,$h )

        ;

    }

    public function save( $cropFilePath, $type, $quality ): bool
    {

        try {
            $this->util->save($cropFilePath, $type, $quality);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

}