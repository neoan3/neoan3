<?php
// Auto-load Neoan3 Components
spl_autoload_register(function ($class) {
    $prefix = 'Neoan3\\Components\\';
    $baseDir = path . '/component/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relativeClass = substr($class, $len);
    $className = str_replace('\\', '/', $relativeClass);
    $file = $baseDir . $className .'/'.$className. '.ctrl.php';

    if (file_exists($file)) {
        require $file;
    }
});
// Auto-load Neoan3 Frames
spl_autoload_register(function ($class) {
    $prefix = 'Neoan3\\Frame\\';
    $baseDir = path . '/frame/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relativeClass = substr($class, $len);
    $className = str_replace('\\', '/', $relativeClass);
    $file = $baseDir . $className .'/'.$className. '.php';

    if (file_exists($file)) {
        require $file;
    }
});