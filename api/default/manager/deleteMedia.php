<?php
/**
 * Description
 * deleteMedia.php
 */

namespace manager;


use APS\ASAPI;
use APS\ASResult;
use APS\Media;

class deleteMedia extends ASAPI{

    private $mediaId   = '';
    public  $mode = 'JSON';

    protected static $groupCharacterRequirement = ['super','manager'];
    protected static $groupLevelRequirement = 80000;

    protected $scope = 'public';

    public function run(): ASResult
    {

        $this->mediaId    = $this->params['mediaId'];

        return Media::common()->delete( $this->mediaId );

    }

}