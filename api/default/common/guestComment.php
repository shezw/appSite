<?php
/**
 * Description
 * guestComment.php
 */

namespace common;

use APS\ASAPI;
use APS\ASResult;
use APS\UserComment;

class guestComment extends ASAPI
{
    const scope = ASAPI_Scope_Public;

    public function run(): ASResult
    {

        $name    = $this->params['name'];
        $email   = $this->params['email'];
        $website = $this->params['website']??'';

        $itemId   = $this->params['itemId'];
        $itemType = $this->params['itemType'];

        if ( !isset($name) || !isset($email) ){

            return $this->error(300,'Name, Email is required');
        }

        if( !($itemId) || !($itemType) ){

            return $this->error(300,'itemId, itemType is required');
        }

        return UserComment::common()->addByArray([
            'userid'=>Group_Guest,
            'itemid'=>$itemId,
            'itemtype'=>$itemType,
            'title'=>$this->params['title']??null,
            'content'=>$this->params['content'],
            'details'=>[
                'name'=>$name,
                'website'=>$website,
                'email'=>$email
            ]
        ]);

    }


}