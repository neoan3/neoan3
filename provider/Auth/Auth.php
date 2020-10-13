<?php


namespace Neoan3\Provider\Auth;


interface Auth
{
    public function setSecret(string $string): void;
    public function validate(?string $provided = null): AuthObjectDeclaration;
    public function restrict($scope = []): AuthObjectDeclaration;
    public function assign($id, $scope, $payload = []): AuthObjectDeclaration;
    public function logout(): bool;
}