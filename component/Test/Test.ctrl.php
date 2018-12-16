<?php
namespace Neoan3\Components;
use Neoan3\Core\Unicore;
/**
* Created by UNICORE-Concr 10/18/2018
*/
use \Neoan3\Apps\Js;
class Test extends Unicore{
    public $alert;
    function init(){
        $this->uni('kit')
            ->addHead('title','Test')
            ->addHead('meta',['charset'=>'utf-8'])
            ->includeElement('my-element')
            ->includeElement('neoan-api')
            ->hook('main','test')
            ->addMethod($this,'alert')
            ->addMethod($this,'click')
            ->addMethod($this,'listen')

            ->alert('before')
            //->listen()
            ->click('this.style.fontSize="30px"')
            ->output();
    }

	function alert($uni,$args=['no text given']){

        $uni->js .= 'alert("'.$args[0].'"); ';
    }
    function listen($uni,$args=[]){
        $js = Js::_()
            ->__('body','apiResponse')
            ->fn('data')
            ->then('console.log(data.detail)')
            ->out();
        $uni->js .= $js;
    }
    function click($uni,$args){
        $js = Js::_()
            ->nfn('resizeMe')
            ->then($args[0])
            ->next()
            ->bind('#button','click')
            ->then('resizeMe')
            ->out();
        $uni->js .= $js;
    }

    function api($obj=[]){
        return ['response'=>'test-data'];
    }




}
