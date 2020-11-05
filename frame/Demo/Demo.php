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
use Neoan3\Provider\Auth\Auth;
use Neoan3\Provider\Auth\SessionWrapper;
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
    private string $dbCredentials = 'testing_db';

    public Auth $Auth;

    /**
     * Demo constructor.
     * @param Database|null $db
     * @param Auth|null $auth
     */
    function __construct(Database $db = null, Auth $auth = null)
    {
        parent::__construct();
        $this->assignProvider('auth', $auth, function (){
            $this->provider['auth'] = new SessionWrapper();
            $this->provider['auth']->setSecret('my-secret');
        });
        $this->Auth = $this->provider['auth'];
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
        $this->renderer->includeElement('customElement');
    }


    /**
     * @return array
     */
    function constants()
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
                'https://cdn.jsdelivr.net/npm/gaudiamus-css@1.2.1/css/gaudiamus.min.css',
            ]
        ];
    }
}
