<?php

####################################################
#
#  Global functions
#
##################################################

/**
 * @param $no
 * @return false or value
 */
function sub($no)
{
    global $route;
    global $serve;
    if ($route && !empty($route->url_parts[$no])) {
        return $route->url_parts[$no];
    } elseif($serve && !empty($request = explode('/',$serve->request))){
        return $request[$no] ?? false;
    } else {
        return false;
    }
}


/**
 * @param string $input
 * @param bool $web
 * @return string
 */
function neoan($input = '', $web = false)
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
function asset($input = '', $web = false)
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
function frame($input = '', $web = false)
{
    if (!$web) {
        return path . '/frame/' . $input;
    } else {
        return base . 'frame/' . $input;
    }
}

/**
 * @param mixed|string $where
 * @param string $method
 * @param bool $get
 * @return bool|string
 */
function redirect($where = base, $method = 'php', $get = false)
{
    if ($method == 'php') {
        header('location: ' . base . '/' . $where . ($get ? '?' . $get : ''));
    } elseif ($method == 'js') {
        return 'window.location = "' . base . '/' . $where . ($get ? '?' . $get : '') . '";';
    }
    return true;
}

/**
 * @return mixed
 * @throws Exception
 */
function getCredentials(){
    $path = DIRECTORY_SEPARATOR .'credentials'.DIRECTORY_SEPARATOR.'credentials.json';
    if(file_exists($path)){
        return json_decode(file_get_contents($path),true);
    } else {
        throw new Exception('Credential location not found');
    }
}
