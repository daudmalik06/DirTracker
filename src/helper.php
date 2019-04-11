<?php
/**
 * Created by PhpStorm.
 * User: dawood.ikhlaq
 * Date: 02/04/2019
 * Time: 15:29
 */

function rootDirectory()
{
    return dirname(dirname(__FILE__));
}

/**
 * @param $key
 * @param null $default
 * @return array|false|null|string
 */
function env($key, $default = null)
{
    $value = getenv($key);
    if ($value === false) {
        $value = $default;
    }
    switch (strtolower($value)) {
        case 'true':
        case '(true)':
            return true;
        case 'false':
        case '(false)':
            return false;
        case 'empty':
        case '(empty)':
            return '';
        case 'null':
        case '(null)':
            return;
    }
    return $value;
}

if ( ! function_exists('glob_recursive'))
{
    // Does not support flag GLOB_BRACE
    function glob_recursive($pattern, $flags = 0)
    {
        $files = glob($pattern, $flags);
        foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir)
        {
            $files = array_merge($files, glob_recursive($dir.'/'.basename($pattern), $flags));
        }
        return $files;
    }
}