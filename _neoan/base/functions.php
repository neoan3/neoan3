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
    if (!empty($route->url_parts[$no])) {
        return $route->url_parts[$no];
    } else {
        return false;
    }
}

/**
 * @param $link
 * @param string $inner
 * @param null $add
 * @return string
 */
function a($link, $inner = '', $add = null)
{
    if ($inner == '') {
        $inner = $link;
    }
    return '<a href="' . $link . '" ' . $add . '>' . $inner . '</a>';
}

/**
 * @param $src
 * @param string $alt
 * @param string $additional
 * @return string
 */
function img($src, $alt = '', $additional = '')
{
    if ($alt == '') {
        $alt = explode('/', $src);
        $alt = explode('.', end($alt));
        $alt = $alt[0];
    }
    if (strpos($additional, 'id="') === false) {
        $autoId = 'id= "' . $alt . '_img"';
    } else {
        $autoId = '';
    }

    return '<img src="' . $src . '" alt="' . $alt . '" ' . $autoid . ' ' . $additional . ' />';
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
