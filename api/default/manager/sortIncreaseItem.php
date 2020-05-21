<?php
/**
 * Description
 * sortIncreaseItem.php
 */

namespace manager;

use APS\ASAPI;
use APS\ASModel;
use APS\ASResult;

class sortIncreaseItem extends ASAPI
{

    private $itemClass = '\APS\ASModel';
    private $itemId = '';
    private $size = 1;

    protected $scope = 'public';
    public $mode = 'JSON';

    protected static $groupCharacterRequirement = ['super', 'manager', 'editor'];
    protected static $groupLevelRequirement = 40000;


    public function run(): ASResult
    {
        $this->itemClass = $this->params['itemClass'] ?? ASModel::class;
        $this->itemId = $this->params['itemId'];
        $this->size = (int)($this->params['size'] ?? 1);

        if (!class_exists($this->itemClass)) {
            $this->itemClass = 'APS\\' . $this->itemClass;
        }

        return $updateItem = $this->itemClass::common()->increaseSort( $this->itemId, $this->size ) ?? ASResult::shared();

    }

}