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

    const mode = ASAPI_Mode_Json;
    const scope = ASAPI_Scope_Public;

    const groupCharacterRequirement = [GroupRole_Super,GroupRole_Manager];
    const groupLevelRequirement = GroupLevel_Admin;

    public function run(): ASResult
    {
        $mediaId = $this->params['mediaId'];

        return Media::common()->delete($mediaId);
    }

}