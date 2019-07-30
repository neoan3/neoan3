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
    $file = $baseDir . lcfirst($className) . '/' . ucfirst($className) . '.ctrl.php';

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
    $file = $baseDir . lcfirst($className) . '/' . ucfirst($className) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});
// Auto-load Neoan3 Models
spl_autoload_register(function ($class) {
    $prefix = 'Neoan3\\Model\\';
    $baseDir = path . '/model/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relativeClass = substr($class, $len);
    $className = substr(str_replace('\\', '/', $relativeClass), 0, strpos($relativeClass, 'Model'));
    $file = $baseDir . lcfirst($className) . '/' . ucfirst($className) . '.model.php';

    if (file_exists($file)) {
        require $file;
    }
});
