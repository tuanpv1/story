<?php
/**
 * Created by PhpStorm.
 * User: Thuc
 * Date: 8/2/2016
 * Time: 4:02 PM
 */

namespace common\helpers;


class LatexHelper
{
    public static function tex2jax($str) {
        $count = substr_count($str, '<div class="latex">');

        if ($count == 0) return $str;

        for ($i = 0; $i < $count; $i++) {
            $pos1 = strpos($str, '<div class="latex">');

            if ($pos1 === FALSE) return $str;

            $pos2 = strpos($str, '</div>', $pos1);

            $resultingString = substr($str, 0, $pos1) . '<div  class="latex" >\(' . substr($str, $pos1 + 19, $pos2 - $pos1 - 19) . '\)</div>' . substr($str, $pos2 + 6);
            $str = $resultingString;
        }

//        return static::tex2jax($resultingString);
        return $str;
    }
}