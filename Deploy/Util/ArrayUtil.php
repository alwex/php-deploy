<?php
/**
 * User: aguidet
 * Date: 10/02/15
 * Time: 15:39
 */

namespace Deploy\Util;

class ArrayUtil {

    public static function getArrayValue(array $array, $key, $default=null) {
        return isset($array[$key]) ? $array[$key] : $default;
    }

}