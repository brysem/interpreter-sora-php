<?php

namespace Bryse\Sora\Support;

class Arr
{
    public static function get(array $array, $key, $default = null)
    {
        if (isset($array[$key])) {
            return $key;
        }

        return $default;
    }
}
