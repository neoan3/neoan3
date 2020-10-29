<?php


namespace Neoan3\Provider\FileSystem;


class File implements Native
{
    public function getContents($path)
    {
        return file_get_contents($path);
    }
    public function putContents($path, $content)
    {
        return file_put_contents($path, $content);
    }
    public function exists($path):bool
    {
        return file_exists($path);
    }
    public function glob($pattern)
    {
        return glob($pattern);
    }

    public function delete(string $path)
    {
        return glob($path);
    }
}