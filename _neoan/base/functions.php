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

function a($link, $inner = '', $add = null)
{
    if ($inner == '') {
        $inner = $link;
    }
    return '<a href="' . $link . '" ' . $add . '>' . $inner . '</a>';
}

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

function neoan($input = '', $web = false)
{
    if (!$web) {
        return neoan_path . '/' . $input;
    } else {
        return base . '_neoan/' . $input;
    }
}

function asset($input = '', $web = false)
{
    if (!$web) {
        return asset_path . '/' . $input;
    } else {
        return base . 'asset/' . $input;
    }
}

function frame($input = '', $web = false)
{
    if (!$web) {
        return path . '/frame/' . $input;
    } else {
        return base . 'frame/' . $input;
    }
}

function redirect($where = base, $method = 'php', $get = false)
{
    if ($method == 'php') {
        header('location: ' . base . '/' . $where . ($get ? '?' . $get : ''));
    } elseif ($method == 'js') {
        return 'window.location = "' . base . '/' . $where . ($get ? '?' . $get : '') . '";';
    }
    return true;
}
