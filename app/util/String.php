<?php
class StringUtil {
    static function wordlimit($string, $length = 50) {
       preg_replace('/<br.*\/?>/i', ' ', $string);
       $string = strip_tags($string);
       $words = explode(' ', $string);
       if (count($words) > $length) {
           return implode(' ', array_slice($words, 0, $length)) . '&hellip;';
       } else {
           return $string;
       }
    }
}