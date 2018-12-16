<?php
/**
* Created by UNICORE-Concr 10/18/2018
* @method $this alert(string $string)
*/
namespace Neoan3\Frame;
use Neoan3\Core\Serve;
use Leafo\ScssPhp as Leafo;
class Frame extends Serve {
    function __construct() {
        parent::__construct();
        define('db_host','localhost');
        define('db_name','neoan');
        define('db_user','root');
        define('db_password','');
    }
    function compiler(){
        $server = new Leafo\Compiler();
        $this->style .= $server->compile(file_get_contents(path.'/component/ApiTest/ApiTest.style.scss'));
        return $this;
    }

    function constants(){
        return [
            'base'=>[base],
            'link'=>[
                [
                    'sizes'=>'32x32',
                    'type'=>'image/png',
                    'rel'=>'icon',
                    'href'=>'http://neoan.us/img/favicon/favicon-32x32.png'
                ]
            ],
            'stylesheet'=>[
                ''.base.'frame/kit/kit.css',
                'https://cdnjs.cloudflare.com/ajax/libs/angular-material/1.1.3/angular-material.min.css',
                'https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,400italic'
            ],
            'meta'=>[
                [
                    'name'=>'viewport',
                    'content'=>'width=device-width, minimum-scale=1, initial-scale=1, user-scalable=yes'
                ],
                [
                    'name'=>'mobile-web-app-capable',
                    'content'=>'yes'
                ],
                [
                    'name'=>'application-name',
                    'content'=>'neoan3'
                ]
            ],
            'js'=>[
               [
                    'src'=>base.'node_modules/@webcomponents/webcomponentsjs/webcomponents-loader.js'
               ],[
                    'src'=>base.'node_modules/@polymer/polymer/polymer-element.js',
                    'type'=>'module'
                ],

               /* [
                    'src'=>base.'vendor/node_modules/@polymer/polymer/polymer-element.js',
                    'data'=>[
                        'api-point'=>''.base.'_neoan/apps/api.app.php',
                        'config'=>'kit',
                        'endpoint'=>current_endpoint,
                        'base'=>base
                    ],
                    'type'=>'module'
                ],*/
                [
                    'src'=>''.base.'frame/kit/kit.js',
                    'data'=>[
                        'base'=>base
                    ]
                ]
            ]
        ];
    }

    static function webApp($shell){
        return '<neoan>'.$shell.'</neoan>';
    }


}
