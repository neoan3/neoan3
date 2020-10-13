<?php


namespace Neoan3\Provider\Auth;


class AuthObject implements AuthObjectDeclaration
{
    private string $userId;
    private array $scope;
    private ?array $payload;
    private string $token = '';

    public function __construct(string $userId, array $scope, ?array $payload = null)
    {
        $this->setUserId($userId);
        $this->setScope($scope);
        $this->setPayload($payload);
    }

    public function setScope(array $scope): void
    {
        $this->scope = $scope;
    }

    public function getScope(): array
    {
        return $this->scope;
    }

    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function setPayload(array $payload): void
    {
        $this->payload = $payload;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }


    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function __toString(): string
    {
        return $this->token;
    }
}