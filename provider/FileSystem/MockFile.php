<?php


namespace Neoan3\Provider\FileSystem;


class MockFile implements Native
{
    private array $registeredContents = [];
    public function getContents($path)
    {
        return $this->registeredContents[$path];
    }
    public function putContents($path, $content)
    {
        $this->registeredContents[$path] = $content;
        return $this->registeredContents[$path];
    }
    public function exists($path):bool
    {
        return isset($this->registeredContents[$path]);
    }
    public function glob($pattern)
    {
        $appliedPattern = str_replace(['*','\\'],['[a-z0-9_-]+','\\\\'], $pattern) ;
        $appliedPattern = '/' . str_replace(['/'],['\/'], $appliedPattern) . '/i';
        $results = [];
        foreach ($this->registeredContents as $path => $content){
            if(preg_match($appliedPattern, $path) === 1){
                $results[] = $path;
            }
        }
        return $results;
    }

    public function delete(string $path)
    {
        unset($this->registeredContents[$path]);
    }
}