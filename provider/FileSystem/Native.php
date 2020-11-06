<?php


namespace Neoan3\Provider\FileSystem;


interface Native
{
    public function getContents(string $path);

    public function putContents(string $path, $content);

    public function exists(string $path): bool;

    public function glob(string $pattern);

    public function delete(string $path);
}