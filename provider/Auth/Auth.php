<?php


namespace Neoan3\Provider\Auth;


interface Auth
{
    public function setSecret(string $string): void;
    public function validate(?string $jwt):array;
    public function restrict(?string $scope):array;
    public function assign($id, $scope, $payload = []):string;
}