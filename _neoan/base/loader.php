<?php

$ds = DIRECTORY_SEPARATOR;

// Auto-load Neoan3 Frames
spl_autoload_register(function ($class) use ($ds)  {
    $prefix = 'Neoan3\\Frame\\';
    $baseDir = path . $ds . 'frame' . $ds;
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relativeClass = substr($class, $len);
    $className = str_replace('\\', $ds, $relativeClass);
    $file = $baseDir . ucfirst($className) . $ds . ucfirst($className) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

