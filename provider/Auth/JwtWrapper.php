<?php


namespace Neoan3\Provider\Auth;


use Exception;
use Neoan3\Apps\Stateless;
use Neoan3\Core\RouteException;

/**
 * Class JwtWrapper
 * @package Neoan3\Provider\Auth
 */
class JwtWrapper implements Auth
{

    /**
     * @param string $secret
     */
    public function setSecret(string $secret): void
    {
        Stateless::setSecret($secret);
    }

    /**
     * @param string|null $provided
     * @return AuthObjectDeclaration
     * @throws RouteException
     */
    public function validate(?string $provided = null): AuthObjectDeclaration
    {
        if($provided){
            Stateless::setAuthorization($provided);
        }
        try{
            $decoded =  Stateless::validate();
            $payload = [];
            foreach ($decoded as $key => $value){
                if(!in_array($key,['iss','aud','iat','jti','scope'])){
                    $payload[$key] = $value;
                }
            }
            $authObject = new AuthObject($decoded['jti'],$decoded['scope'], $payload);
            $authObject->setToken(Stateless::getAuthorization());
            return $authObject;
        } catch (Exception $e) {
            throw new RouteException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param ?array $scope
     * @return AuthObjectDeclaration
     * @throws RouteException
     */
    public function restrict($scope = []): AuthObjectDeclaration
    {
        try{
            Stateless::restrict($scope);
            return $this->validate();
        } catch (Exception $e) {
            throw new RouteException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param $id
     * @param $scope
     * @param array $payload
     * @return AuthObjectDeclaration
     * @throws RouteException
     */
    public function assign($id, $scope, $payload = []): AuthObjectDeclaration
    {
        try{
            $jwt = Stateless::assign($id, $scope, $payload);
            return $this->validate($jwt);
        } catch (Exception $e) {
            throw new RouteException($e->getMessage(), $e->getCode());
        }
    }

    public function logout(): bool
    {
        return false;
    }
}