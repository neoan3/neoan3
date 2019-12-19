<?php

$ds = DIRECTORY_SEPARATOR;


// Auto-load Neoan3 Core (in base)
spl_autoload_register(function ($class) use ($ds) {
    $prefix = 'Neoan3\\Core\\';
    $baseDir = path . $ds . '_neoan' . $ds;
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relativeClass = substr($class, $len);
    $className = str_replace('\\', '/', $relativeClass);
    foreach (['base','layout'] as $coreDirectory){
        $file = $baseDir . $coreDirectory . $ds . ucfirst($className) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    }

});

// Auto-load Neoan3 Components
spl_autoload_register(function ($class) use ($ds)  {
    $prefix = 'Neoan3\\Components\\';
    $baseDir = path . $ds . 'component' .$ds;
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relativeClass = substr($class, $len);
    $className = str_replace('\\', $ds, $relativeClass);
    $file = $baseDir . lcfirst($className) . $ds . ucfirst($className) . '.ctrl.php';

    if (file_exists($file)) {
        require $file;
    }
});
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
    $file = $baseDir . lcfirst($className) . $ds . ucfirst($className) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});
// Auto-load Neoan3 Models
spl_autoload_register(function ($class)  use ($ds) {
    $prefix = 'Neoan3\\Model\\';
    $baseDir = path . $ds . 'model' . $ds;
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relativeClass = substr($class, $len);
    $className = substr(str_replace('\\', $ds, $relativeClass), 0, strpos($relativeClass, 'Model'));
    $possible = ['.model.php','.transformer.php'];
    foreach ($possible as $ending){
        $file = $baseDir . lcfirst($className) . $ds . ucfirst($className) . $ending;

        if (file_exists($file)) {
            require_once $file;
        }
    }

});
