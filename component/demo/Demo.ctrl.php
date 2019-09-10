<?php

namespace Neoan3\Components;

use Neoan3\Apps\Db;
use Neoan3\Core\Unicore;
use Neoan3\Model;
use Neoan3\Model\UserModel;

class Demo extends Unicore
{
    function init()
    {
        $this->uni('demo')
             ->hook('main', 'demo')->callback($this,'test')
             ->output();
    }
    function test(){
        Db::setEnvironment(['name'=>'db_app','assumes_uuid'=>true]);
        $id = Model\IndexModel::first(Db::easy('user.id'))['id'];
        $in =
            [
                'email' => [
                    'email' => 'some@other.com'
                ],
                'password' => [
                    'password' => 'foobarbaz'
                ],
                'userName' => 'sam'
            ];
                UserModel::create($in);

        var_dump(UserModel::get($id));
        die();
    }
}
