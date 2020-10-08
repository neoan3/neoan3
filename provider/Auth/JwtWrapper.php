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
     * @param string|null $jwt
     * @return array
     * @throws RouteException
     */
    public function validate(?string $jwt): array
    {
        if($jwt){
            Stateless::setAuthorization($jwt);
        }
        try{
            return Stateless::validate();
        } catch (Exception $e) {
            throw new RouteException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param string|null $scope
     * @return array
     * @throws RouteException
     */
    public function restrict(string $scope = null): array
    {
        try{
            return Stateless::restrict($scope);
        } catch (Exception $e) {
            throw new RouteException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param $id
     * @param $scope
     * @param array $payload
     * @return string
     * @throws RouteException
     */
    public function assign($id, $scope, $payload = []): string
    {
        try{
            return Stateless::assign($id, $scope, $payload);
        } catch (Exception $e) {
            throw new RouteException($e->getMessage(), $e->getCode());
        }
    }
}