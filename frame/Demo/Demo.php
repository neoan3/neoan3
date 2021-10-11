<?php
/**
 * Created by PhpStorm.
 * User: sroehrl
 * Date: 2/4/2019
 * Time: 1:36 PM
 */

namespace Neoan3\Frame;

use Exception;
use Neoan3\Core\Serve;
use Neoan3\Provider\Attributes\UseAttributes;
use Neoan3\Provider\Auth\Auth;
use Neoan3\Provider\Auth\AuthObject;
use Neoan3\Provider\Auth\JwtWrapper;
use Neoan3\Provider\MySql\Database;
use Neoan3\Provider\MySql\DatabaseWrapper;

/**
 * Class Demo
 * @package Neoan3\Frame
 * @property Auth $auth
 */
class Demo extends Serve
{

    /**
     * Name your credentials
     * @var string
     */
    private string $dbCredentials = 'neoan3_db';

    public Auth $Auth;

    public ?AuthObject $authObject;

    /**
     * Demo constructor.
     * @param Database|null $db
     * @param Auth|null $auth
     */
    function __construct(Database $db = null, Auth $auth = null)
    {
        parent::__construct();
        $this->Auth = $this->assignProvider('auth', $auth, function (){
            $auth = new JwtWrapper();
            $auth->setSecret('my-secret123');
            return $auth;
        });

        $this->assignProvider('db', $db, function(){
            try{
                $credentials = getCredentials();
                if(isset($credentials[$this->dbCredentials])){
                    $this->provider['db'] = new DatabaseWrapper($credentials[$this->dbCredentials]);
                }
            } catch (Exception $e) {
                $this->renderer->addToHead('title', '! No credentials found! Run "neoan3 new database '. $this->dbCredentials .'"');
            }
        });

        /*
         * PHP8 Attributes
         * */
        if(PHP_MAJOR_VERSION >= 8){
            $phpAttributes = new UseAttributes();
            $phpAttributes->hookAttributes($this->provider);
            $this->authObject = $phpAttributes->authObject;
        }

        $this->renderer->includeElement('customElement');
        $this->hook('header', 'nav');
    }


    /**
     * @return array
     */
    function constants(): array
    {
        return [
            'base' => [base],
            'title' => ['Default Title'],
            'link' => [
                [
                    'sizes' => '32x32',
                    'type' => 'image/png',
                    'rel' => 'icon',
                    'href' => 'asset/neoan-favicon.png'
                ]
            ],
            'stylesheet' => [
                '' . base . 'frame/Demo/demo.css',
                'https://cdn.jsdelivr.net/npm/gaudiamus-css@latest/css/gaudiamus.min.css',
            ]
        ];
    }
}
