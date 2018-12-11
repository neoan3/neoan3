<?php
namespace Neoan3\Components;
use Neoan3\Core\Unicore;
use Neoan3\Apps\Db;
use Neoan3\Apps\Js;

class ApiTest extends Unicore {
    function init(){
        $this->uni('kit')
            ->hook('main','ApiTest')
            ->includeElement('neoan-api')
            ->callback($this,'listen')
            ->callback(new Test,'alert')
            ->compiler()->output();

    }
    function ctrl($uni,$args=[]){

    }
    function test($obj=[]){
        $test = Db::data('SELECT NOW()')['data'];
        return $test;
    }
    function listen($uni,$args=[]){
        $uni->js .= Js::_()
            ->bind('body','apiResponse')
            ->fn('data')
            ->then('console.log(data.detail)')
            ->out();
    }


}