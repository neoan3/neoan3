<?php


namespace Neoan3\Provider\Auth;



use Exception;
use Neoan3\Apps\Session;

/**
 * Class SessionWrapper
 * @package Neoan3\Provider\Auth
 */
class SessionWrapper implements Auth
{

    /**
     * SessionWrapper constructor.
     * @param null $secret
     */
    public function __construct($secret = null)
    {
        if($secret){
            $this->setSecret($secret);
        }
    }

    /**
     * @param string $string
     */
    public function setSecret(string $string): void
    {
        new Session($string);
    }

    /**
     * @param string|null $provided
     * @return AuthObjectDeclaration
     * @throws Exception
     */
    public function validate(?string $provided = null): AuthObjectDeclaration
    {
        if(Session::isLoggedIn()){
            $user = Session::getUserSession();
            return new AuthObject($user['logged_id'], $user['scope'], $user['payload']);
        } else {
            throw new Exception('Unauthenticated', 401);
        }
    }

    /**
     * @param array $scope
     * @return AuthObjectDeclaration
     * @throws Exception
     */
    public function restrict($scope = []): AuthObjectDeclaration
    {
        $trueScope = empty($scope) ? null : $scope;
        try{
            Session::restrict($trueScope);
            return $this->validate();
        } catch (Exception $e){
            throw new Exception('Unauthenticated', 401);
        }

    }

    /**
     * @param $id
     * @param $scope
     * @param array $payload
     * @return AuthObjectDeclaration
     * @throws Exception
     */
    public function assign($id, $scope, $payload = []): AuthObjectDeclaration
    {
        Session::login($id,$scope);
        if(!empty($payload)){
            Session::addToSession(['payload'=>$payload]);
        }
        return $this->validate();
    }

    /**
     * @return bool
     */
    public function logout():bool
    {
        Session::logout();
        return true;
    }
}