<?php
class ArrayUtil {
    static function indexBy($array, $key, $bucket = false) {
        $response = array();
        foreach ($array as $value) {
            $key_value = self::getProperty($value, $key);
            if ($bucket) {
                if (!isset($response[$key_value])) {
                    $response[$key_value] = array();
                }
                $response[$key_value][] = $value;
            }
            else {
                $response[$key_value] = $value;
            }
        }
        return $response;
    }

    static function indexByBucketed($array, $key) {
        return self::indexBy($array, $key, true);
    }

    static function getProperty($item, $key) {
        if (is_array($item)) {
            return $item[$key];
        }
        return $item->$key;
    }
}