<?php
/**
 * Description
 * updateItem.php
 */

namespace manager;

use APS\ASAPI;
use APS\ASModel;
use APS\ASResult;
use APS\User;

class updateUser extends ASAPI
{

    private $userId = '';
    private $data   = [];

    protected $scope = 'public';
    public  $mode = 'JSON';

    protected static $groupCharacterRequirement = ['super','manager','editor'];
    protected static $groupLevelRequirement = 80000;

    public function run(): ASResult
    {
        $this->userId    = $this->params['userId'];
        $this->data      = $this->params['data'];

        $user = new User( $this->userId );

        return $user->update( $this->data , $this->userId );

    }

}