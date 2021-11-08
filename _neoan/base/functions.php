<?php

####################################################
#
#  Global functions
#
##################################################

/**
 * @param int $num
 * @return string|null
 */
function routePart(int $num) : ?string
{
    return sub($num);
}

/**
 * Deprecated: use routePart(int $num): ?string
 * @param $no
 * @return string|null or value
 */
function sub($no): ?string
{
    global $route;
    global $serve;
    global $api;
    if ($route && !empty($route->url_parts[$no])) {
        return $route->url_parts[$no];
    } elseif($serve && !empty($request = explode('/',$serve->request))){
        return $request[$no] ?? false;
    } elseif($api && !empty($request = explode('/',$_SERVER['REQUEST_URI']))){
        array_shift($request);
        return $request[$no] ?? false;
    } else {
        return null;
    }
}


/**
 * @param string $input
 * @param bool $web
 * @return string
 */
function neoan(string $input = '', bool $web = false)
{
    if (!$web) {
        return neoan_path . '/' . $input;
    } else {
        return base . '_neoan/' . $input;
    }
}

/**
 * @param string $input
 * @param bool $web
 * @return string
 */
function asset(string $input = '', bool $web = false): string
{
    if (!$web) {
        return asset_path . '/' . $input;
    } else {
        return base . 'asset/' . $input;
    }
}

/**
 * @param string $input
 * @param bool $web
 * @return string
 */
function frame(string $input = '', bool $web = false): string
{
    if (!$web) {
        return path . '/frame/' . $input;
    } else {
        return base . 'frame/' . $input;
    }
}

/**
 * @param string|null $where
 * @param string $method
 * @param bool $get
 * @return bool|string
 */
function redirect(string $where = null, string $method = 'php', bool $get = false)
{
    if ($method == 'php') {
        header('location: ' . base . $where . ($get ? '?' . $get : ''));
    } elseif ($method == 'js') {
        return 'window.location = "' . base . $where . ($get ? '?' . $get : '') . '";';
    }
    return true;
}

/**
 * @param string $path
 * @return mixed
 * @throws Exception
 */
function getCredentials(string $path = DIRECTORY_SEPARATOR .'credentials'.DIRECTORY_SEPARATOR.'credentials.json')
{
    if(file_exists($path)){
        return json_decode(file_get_contents($path),true);
    } else {
        throw new Exception('Credential location not found');
    }
}
